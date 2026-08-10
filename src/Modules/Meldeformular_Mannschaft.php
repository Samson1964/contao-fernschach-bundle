<?php

/**
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Modules;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Email;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use Schachbulle\ContaoFernschachBundle\Classes\Helper;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

/**
 * Frontend-Modul: Meldung einer Mannschaft zu einem Mannschaftsturnier.
 *
 * Das Formular baut seit Version 2.2.0 eigenes Markup auf und bringt sein
 * eigenes Aussehen und Verhalten mit (fernschach_formular.css und
 * mannschaftsmeldung.js). Vorher entstand es über die Formularklasse des
 * Helper-Bundles und übernahm Gestaltung und Skripte vom jeweiligen Theme —
 * mit dem Ergebnis, dass es auf jeder Website anders aussah und die
 * Spielerauswahl aus vier Auswahllisten mit sämtlichen Mitgliedern bestand.
 */
class Meldeformular_Mannschaft extends Module
{

	protected $strTemplate = 'mod_fernschach_mannschaft';

	/**
	 * Zeigt im Backend einen Platzhalter statt der eigentlichen Ausgabe.
	 *
	 * @return string Der gerenderte Platzhalter im Backend, sonst die
	 *                Frontend-Ausgabe des Moduls
	 */
	public function generate()
	{
		if (Scope::isBackendRequest())
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH MELDEFORMULAR MANNSCHAFTEN ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	/**
	 * Füllt das Template mit allem, was die Ausgabe braucht.
	 *
	 * Ist das Modul an die Mitgliedschaft gebunden, wird vorher geprüft, ob der
	 * angemeldete Benutzer überhaupt ein verifiziertes BdF-Mitglied ist.
	 *
	 * @return void Die Ausgabe entsteht über $this->Template
	 */
	protected function compile()
	{
		$this->Template->fehlertext = '';
		$this->Template->fehler = array();
		$this->Template->bestaetigung = null;
		$this->Template->turniere = array();
		$this->Template->mitglied = null;
		$this->Template->werte = array();
		$this->Template->suchadresse = '';

		// Eigene Gestaltung und eigenes Verhalten einbinden
		$GLOBALS['TL_CSS']['fernschach_formular'] = 'bundles/contaofernschach/css/fernschach_formular.css';
		$GLOBALS['TL_JAVASCRIPT']['fernschach_mannschaft'] = 'bundles/contaofernschach/js/mannschaftsmeldung.js|static';

		if ($this->fernschachverwaltung_linkingMembers)
		{
			// Das Formular darf nur BdF-Mitgliedern angezeigt werden
			$this->import(FrontendUser::class, 'User');

			if (!$this->User)
			{
				$this->Template->fehlertext = 'Zugriff auf das Formular nicht erlaubt, da Sie nicht angemeldet sind.';

				return;
			}

			if (!$this->User->isMemberOf(Config::get('fernschach_memberFernschach')))
			{
				$this->Template->fehlertext = 'Zugriff auf das Formular nicht erlaubt, da kein verifiziertes BdF-Mitglied.';

				return;
			}
		}

		// BdF-Mitgliedsdaten des angemeldeten Benutzers laden
		$mitglied = Helper::getSpielerdatensatz(FrontendUser::getInstance()->fernschach_memberId);

		if (!$mitglied || !$mitglied->numRows)
		{
			$this->Template->fehlertext = 'Ihrem Benutzerkonto ist kein BdF-Mitglied zugeordnet. Bitte wenden Sie sich an die Geschäftsstelle.';

			return;
		}

		$this->Template->mitglied = $mitglied;
		$this->Template->beitragssaldo = Helper::getBeitragssaldo($mitglied->id);

		// Rückkehr von der Umleitung nach dem Absenden
		if (Input::get('send'))
		{
			$this->Template->bestaetigung = Scope::getSessionValue('fernschach_mannschaftsmeldung');
			Scope::setSessionValue('fernschach_mannschaftsmeldung', null);

			return;
		}

		$turniere = self::getTournaments($mitglied);
		$this->Template->turniere = $turniere;

		// Adresse der Autovervollständigung. Über den Router ermittelt, damit sie
		// auch dann stimmt, wenn die Website in einem Unterverzeichnis liegt.
		$this->Template->suchadresse = System::getContainer()->get('router')->generate('contao_fernschach_spieler_suche');

		if (!$turniere)
		{
			$this->Template->fehlertext = 'Zurzeit steht kein Mannschaftsturnier zur Meldung offen. Möglicherweise haben Sie für alle offenen Turniere bereits gemeldet.';

			return;
		}

		// Abgeschickte Werte übernehmen, damit sie bei einem Fehler erhalten bleiben
		$werte = self::leseEingaben($turniere);
		$this->Template->werte = $werte;
		$this->Template->requestToken = Scope::getRequestToken();

		if (Input::post('FORM_SUBMIT') !== 'fernschach_mannschaftsmeldung')
		{
			return;
		}

		$fehler = self::pruefeEingaben($werte, $turniere, $mitglied);
		$this->Template->fehler = $fehler;

		if ($fehler)
		{
			return;
		}

		if (!self::saveMeldung($werte, $turniere[$werte['turnier']], $mitglied))
		{
			$this->Template->fehler = array('turnier' => 'Ihre Meldung konnte nicht gespeichert werden. Möglicherweise haben Sie für dieses Turnier bereits eine Mannschaft gemeldet.');

			return;
		}

		// Nach dem Speichern umleiten statt neu laden, damit ein Aktualisieren
		// die Meldung nicht ein zweites Mal abschickt.
		Controller::redirect(Controller::addToUrl('send=1'));
	}

	/**
	 * Liest die abgeschickten Formularwerte ein.
	 *
	 * Die Bretter werden anhand des gewählten Turniers eingelesen, nicht anhand
	 * einer festen Zahl — je nach Turnier sind es vier, sechs oder acht.
	 *
	 * @param array $turniere Die zur Auswahl stehenden Turniere, Schlüssel ist die ID
	 *
	 * @return array Feld mit den Schlüsseln 'turnier', 'vereinsname',
	 *               'vereinsname_alt', 'mannschaftsname', 'bemerkungen',
	 *               'bretter' (Anzahl) und 'spieler' (Brettnummer => Spieler-ID)
	 */
	protected function leseEingaben($turniere)
	{
		$turnier = (int) Input::post('turnier');
		$bretter = isset($turniere[$turnier]) ? (int) $turniere[$turnier]['bretter'] : 0;

		$spieler = array();
		$namen = array();

		for ($brett = 1; $brett <= $bretter; ++$brett)
		{
			$id = (int) Input::post('spieler_'.$brett);
			$spieler[$brett] = $id;

			// Anzeigenamen nachschlagen, damit nach einem Fehler wieder im Feld
			// steht, was der Benutzer ausgewählt hatte
			$namen[$brett] = '';

			if ($id)
			{
				$objSpieler = Helper::getSpielerdatensatz($id);

				if ($objSpieler && $objSpieler->numRows)
				{
					$namen[$brett] = $objSpieler->nachname.', '.$objSpieler->vorname;
				}
			}
		}

		return array
		(
			'namen'           => $namen,
			'turnier'         => $turnier,
			'vereinsname'     => trim((string) Input::post('vereinsname')),
			'vereinsname_alt' => trim((string) Input::post('vereinsname_alt')),
			'mannschaftsname' => trim((string) Input::post('mannschaftsname')),
			'bemerkungen'     => trim((string) Input::post('bemerkungen')),
			'bretter'         => $bretter,
			'spieler'         => $spieler,
		);
	}

	/**
	 * Prüft die abgeschickten Werte.
	 *
	 * Geprüft wird alles, was auch das Skript im Browser prüft — der Browser ist
	 * bequem, aber keine Sicherung: Formulare lassen sich ohne ihn abschicken.
	 *
	 * @param array  $werte    Die eingelesenen Formularwerte
	 * @param array  $turniere Die zur Auswahl stehenden Turniere
	 * @param object $mitglied Spielerdatensatz des Mannschaftsführers
	 *
	 * @return array Feldname => Fehlermeldung; leer, wenn alles stimmt. Für die
	 *               Bretter lautet der Schlüssel 'spieler_<Brettnummer>'
	 */
	protected function pruefeEingaben($werte, $turniere, $mitglied)
	{
		$fehler = array();

		if (!$werte['turnier'] || !isset($turniere[$werte['turnier']]))
		{
			$fehler['turnier'] = 'Bitte wählen Sie ein Turnier aus.';

			// Ohne Turnier lässt sich die Zahl der Bretter nicht bestimmen
			return $fehler;
		}

		if ('' === $werte['vereinsname'])
		{
			$fehler['vereinsname'] = 'Bitte geben Sie den Namen des Vereins oder der Spielgemeinschaft an.';
		}

		if ('' === $werte['mannschaftsname'])
		{
			$fehler['mannschaftsname'] = 'Bitte geben Sie die Bezeichnung der Mannschaft an.';
		}

		// Spieler prüfen: vorhanden, meldefähig und nicht doppelt
		$vergeben = array();

		foreach ($werte['spieler'] as $brett => $id)
		{
			if (!$id)
			{
				$fehler['spieler_'.$brett] = 'Bitte wählen Sie einen Spieler aus der Vorschlagsliste aus.';

				continue;
			}

			if (isset($vergeben[$id]))
			{
				$fehler['spieler_'.$brett] = 'Dieser Spieler steht bereits an Brett '.$vergeben[$id].'.';

				continue;
			}

			$objSpieler = Helper::getSpielerdatensatz($id);

			if (!$objSpieler || !$objSpieler->numRows || !Helper::checkMembership($objSpieler))
			{
				$fehler['spieler_'.$brett] = 'Dieser Spieler ist kein meldefähiges BdF-Mitglied.';

				continue;
			}

			$vergeben[$id] = $brett;
		}

		return $fehler;
	}

	/**
	 * Speichert die Meldung und verschickt die beiden E-Mails.
	 *
	 * Gespeichert wird ausschließlich die Nenngeld-Sollbuchung auf dem Konto des
	 * Mannschaftsführers; die Aufstellung selbst geht per E-Mail an ihn und an
	 * den Turnierdirektor. Zusätzlich landet eine Zusammenfassung in der Sitzung,
	 * damit die Bestätigungsseite sie anzeigen kann.
	 *
	 * @param array  $werte    Die geprüften Formularwerte
	 * @param array  $turnier  Der gewählte Turniereintrag aus getTournaments()
	 * @param object $mitglied Spielerdatensatz des Mannschaftsführers
	 *
	 * @return bool True, wenn gespeichert wurde. False, wenn es das Turnier nicht
	 *              mehr gibt oder für dieses Turnier bereits gemeldet wurde
	 */
	protected function saveMeldung($werte, $turnier, $mitglied)
	{
		$objTurnier = Helper::getTurnierdatensatz($werte['turnier']);

		if (!$objTurnier || !$objTurnier->numRows)
		{
			return false;
		}

		// Zweite Prüfung nach dem Absenden: Der Browser kann dieselbe Meldung
		// über den Zurück-Knopf oder einen zweiten Tab erneut schicken.
		if (self::bereitsGemeldet($objTurnier, $mitglied->id))
		{
			Scope::log(
				'[Fernschach-Verwaltung] Mehrfache Mannschaftsmeldung abgewiesen: '.$mitglied->nachname.', '.$mitglied->vorname.' (ID '.$mitglied->id.') für Turnier '.$objTurnier->title.' (ID '.$objTurnier->id.')',
				__METHOD__,
				ContaoContext::GENERAL
			);

			return false;
		}

		// Namen der gemeldeten Spieler nachschlagen
		$aufstellung = array();

		foreach ($werte['spieler'] as $brett => $id)
		{
			$objSpieler = Helper::getSpielerdatensatz($id);

			$aufstellung[$brett] = $objSpieler->nachname.', '.$objSpieler->vorname
				.' (BdF-Nr. '.$objSpieler->memberId
				.($objSpieler->memberInternationalId ? ' / ICCF-ID '.$objSpieler->memberInternationalId : '').')';
		}

		// Nenngeldbuchung Soll erzeugen
		$zeit = time();

		Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler_konto_nenngeld %s')
		                       ->set(array
		                       (
		                       	'pid'              => $mitglied->id,
		                       	'tstamp'           => $zeit,
		                       	'betrag'           => $objTurnier->nenngeld,
		                       	'typ'              => 's',
		                       	'datum'            => $zeit,
		                       	'kategorie'        => 's',
		                       	'art'              => 'n',
		                       	'verwendungszweck' => 'Nenngeld-Forderung '.$objTurnier->title,
		                       	'turnier'          => $werte['turnier'],
		                       	'comment'          => 'Datensatz erzeugt durch Mannschaftsmeldung am '.date('d.m.Y H:i', $zeit),
		                       	'meldungId'        => 0,
		                       	'published'        => '1',
		                       ))
		                       ->execute();

		// Zusammenfassung für die Bestätigungsseite und die E-Mails
		$zusammenfassung = array
		(
			'zeit'            => $zeit,
			'turnier'         => $objTurnier->title,
			'nenngeld'        => $turnier['nenngeld'],
			'vereinsname'     => $werte['vereinsname'],
			'vereinsname_alt' => $werte['vereinsname_alt'],
			'mannschaftsname' => $werte['mannschaftsname'],
			'bemerkungen'     => $werte['bemerkungen'],
			'fuehrer'         => $mitglied->nachname.', '.$mitglied->vorname,
			'fuehrer_bdf'     => $mitglied->memberId,
			'fuehrer_iccf'    => $mitglied->memberInternationalId,
			'fuehrer_email'   => $mitglied->email1,
			'aufstellung'     => $aufstellung,
		);

		Scope::setSessionValue('fernschach_mannschaftsmeldung', $zusammenfassung);

		self::sendeMails($zusammenfassung, $mitglied);

		return true;
	}

	/**
	 * Verschickt die Bestätigung an den Mannschaftsführer und den Turnierdirektor.
	 *
	 * Beide bekommen denselben Inhalt; nur die Einleitung unterscheidet sich.
	 *
	 * @param array  $daten    Die Zusammenfassung aus saveMeldung()
	 * @param object $mitglied Spielerdatensatz des Mannschaftsführers
	 *
	 * @return void Ist beim Turnierdirektor keine Adresse hinterlegt, geht nur
	 *              die Bestätigung an den Mannschaftsführer heraus
	 */
	protected function sendeMails($daten, $mitglied)
	{
		$empfaenger = array
		(
			array($mitglied->vorname.' '.$mitglied->nachname.' <'.$mitglied->email1.'>', 'Sie haben eine Mannschaft zu einem Mannschaftsturnier gemeldet:'),
		);

		if (Config::get('fernschach_turnierdirektorEmail'))
		{
			$empfaenger[] = array(
				Config::get('fernschach_turnierdirektorName').' <'.Config::get('fernschach_turnierdirektorEmail').'>',
				'Eine neue Mannschaftsmeldung wurde vorgenommen:',
			);
		}

		foreach ($empfaenger as $eintrag)
		{
			$objEmail = new Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = Config::get('fernschach_emailAdresse');
			$objEmail->fromName = Config::get('fernschach_emailVon');
			$objEmail->subject = 'Mannschaftsmeldung '.$daten['vereinsname'];
			$objEmail->html = self::baueMailtext($daten, $eintrag[1]);
			$objEmail->sendTo(array($eintrag[0]));
		}
	}

	/**
	 * Baut den HTML-Text der Bestätigungsmail.
	 *
	 * @param array  $daten      Die Zusammenfassung aus saveMeldung()
	 * @param string $einleitung Der erste Satz, je nach Empfänger unterschiedlich
	 *
	 * @return string Vollständiges HTML-Dokument
	 */
	protected function baueMailtext($daten, $einleitung)
	{
		$text = '<html><head><title></title></head><body>';
		$text .= '<p>'.StringUtil::specialchars($einleitung).'</p>';
		$text .= '<h3>Turnier</h3><ul>';
		$text .= '<li>Turnier: <b>'.StringUtil::specialchars($daten['turnier']).'</b></li>';
		$text .= '<li>Meldezeit: <b>'.date('d.m.Y H:i', $daten['zeit']).'</b></li>';
		$text .= '<li>Nenngeld: <b>'.StringUtil::specialchars($daten['nenngeld']).'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Mannschaft</h3><ul>';
		$text .= '<li>Verein: <b>'.StringUtil::specialchars($daten['vereinsname']).'</b></li>';

		if ($daten['vereinsname_alt'])
		{
			$text .= '<li>Alter Vereinsname: <b>'.StringUtil::specialchars($daten['vereinsname_alt']).'</b></li>';
		}

		$text .= '<li>Mannschaft: <b>'.StringUtil::specialchars($daten['mannschaftsname']).'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Mannschaftsführer</h3><ul>';
		$text .= '<li>Name: <b>'.StringUtil::specialchars($daten['fuehrer']).'</b></li>';
		$text .= '<li>BdF-Mitgliedsnummer: <b>'.StringUtil::specialchars((string) $daten['fuehrer_bdf']).'</b></li>';
		$text .= '<li>ICCF-ID: <b>'.StringUtil::specialchars((string) $daten['fuehrer_iccf']).'</b></li>';
		$text .= '<li>E-Mail: <b>'.StringUtil::specialchars((string) $daten['fuehrer_email']).'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Aufstellung</h3><ul>';

		foreach ($daten['aufstellung'] as $brett => $name)
		{
			$text .= '<li>Brett '.(int) $brett.': <b>'.StringUtil::specialchars($name).'</b></li>';
		}

		$text .= '</ul>';

		if ($daten['bemerkungen'])
		{
			$text .= '<h3>Bemerkungen</h3><p>'.nl2br(StringUtil::specialchars($daten['bemerkungen'])).'</p>';
		}

		$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';

		return $text;
	}

	/**
	 * Stellt die Turniere zusammen, für die gemeldet werden darf.
	 *
	 * Berücksichtigt werden veröffentlichte Mannschaftsturniere mit aktiver
	 * Online-Anmeldung, deren Meldeschluss noch nicht verstrichen ist. Turniere,
	 * für die der Mannschaftsführer die zulässige Zahl an Meldungen bereits
	 * erreicht hat, fallen heraus.
	 *
	 * @param object $mitglied Spielerdatensatz des Mannschaftsführers
	 *
	 * @return array Turnier-ID => Feld mit 'id', 'title', 'bretter', 'nenngeld'
	 *               und 'meldeschluss'
	 */
	public function getTournaments($mitglied)
	{
		$turniere = array();
		$heute = mktime(0, 0, 0);

		$objTurniere = Database::getInstance()->prepare(
			'SELECT * FROM tl_fernschach_turniere'
			.' WHERE (registrationDate >= ? OR registrationDate = ?) AND onlineAnmeldung = ? AND published = ? AND typ = ?'
			.' ORDER BY title ASC'
		)->execute($heute, 0, '1', '1', 'm');

		while ($objTurniere->next())
		{
			// Turniere überspringen, für die dieser Mannschaftsführer schon
			// gemeldet hat (siehe self::bereitsGemeldet)
			if (self::bereitsGemeldet($objTurniere, $mitglied->id))
			{
				continue;
			}

			$turniere[(int) $objTurniere->id] = array
			(
				'id'           => (int) $objTurniere->id,
				'title'        => $objTurniere->title,
				// Ohne Angabe am Turnier bleibt es bei den üblichen vier Brettern
				'bretter'      => max(1, (int) ($objTurniere->bretter ?: 4)),
				'nenngeld'     => trim(str_replace('.', ',', sprintf('%0.2f', (float) $objTurniere->nenngeld))).' €',
				'meldeschluss' => $objTurniere->registrationDate ? date('d.m.Y', (int) $objTurniere->registrationDate) : '',
			);
		}

		return $turniere;
	}

	/**
	 * Prüft, ob ein Mannschaftsführer für ein Turnier bereits gemeldet hat.
	 *
	 * Eine Mannschaftsmeldung legt keinen Datensatz in den Anmeldungen an — sie
	 * hinterlässt als einzige Spur eine Nenngeld-Sollbuchung auf dem Konto des
	 * Mannschaftsführers. Genau danach wird hier gesucht.
	 *
	 * Wie oft gemeldet werden darf, steht wie bei den Einzelturnieren am Turnier
	 * im Feld maxMeldungen; 0 bedeutet unbegrenzt.
	 *
	 * @param object     $objTurnier Turnierdatensatz mit den Feldern id und maxMeldungen
	 * @param int|string $spieler    ID des Mannschaftsführers aus tl_fernschach_spieler
	 *
	 * @return bool True, wenn die zulässige Zahl an Meldungen erreicht ist
	 */
	protected static function bereitsGemeldet($objTurnier, $spieler)
	{
		$intMax = (int) ($objTurnier->maxMeldungen ?? 0);

		if ($intMax < 1 || !$spieler)
		{
			return false;
		}

		$objBuchungen = Database::getInstance()->prepare('SELECT COUNT(*) AS anzahl FROM tl_fernschach_spieler_konto_nenngeld WHERE pid = ? AND turnier = ? AND typ = ? AND kategorie = ?')
		                                        ->execute($spieler, $objTurnier->id, 's', 's');

		return (int) $objBuchungen->anzahl >= $intMax;
	}
}
