<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021-2023 Frank Hoppe
 *
 * @package   Fernschach-Verwaltung
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2021-2023
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
use Schachbulle\ContaoFernschachBundle\Classes\Helper;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

class Meldeformular_Spieler extends Module
{

	protected $strTemplate = 'mod_fernschach_meldeformular';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (Scope::isBackendRequest())
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH MELDEFORMULAR SPIELER ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Füllt das Template mit allem, was die Ausgabe braucht.
	 *
	 * Seit Version 2.7.0 baut das Modul eigenes Markup auf und bringt sein
	 * Aussehen selbst mit. Vorher entstand das Formular über die Formularklasse
	 * des Helper-Bundles, und die Mitgliedsdaten waren als HTML-Zeichenkette mit
	 * `<span style="color:green">` einprogrammiert — auf jeder Website sah es
	 * anders aus.
	 *
	 * @return void Die Ausgabe entsteht über $this->Template
	 */
	protected function compile()
	{
		$this->Template->fehlertext = '';
		$this->Template->bestaetigung = null;
		$this->Template->mitglied = null;
		$this->Template->turniere = array();
		$this->Template->werte = array();
		$this->Template->fehler = array();
		$this->Template->qualifikationen = array();
		$this->Template->meldungen = array();
		$this->Template->konten = array();
		$this->Template->sepa = array();
		$this->Template->begriff = $this->fernschachverwaltung_bewerbung ? 'Bewerbung' : 'Anmeldung';
		$this->Template->einleitung = $this->fernschachverwaltung_tournamentText;
		$this->Template->radio = (bool) $this->fernschachverwaltung_radio;
		$this->Template->requestToken = Scope::getRequestToken();

		// Eigene Gestaltung einbinden. Ein Skript braucht dieses Formular nicht:
		// Es hat keine dynamischen Felder, und die Prüfung gehört ohnehin auf
		// den Server.
		$GLOBALS['TL_CSS']['fernschach_formular'] = 'bundles/contaofernschach/css/fernschach_formular.css';

		if($this->fernschachverwaltung_linkingMembers)
		{
			$this->import(FrontendUser::class, 'User');

			if(!$this->User)
			{
				$this->Template->fehlertext = 'Zugriff auf das Formular nicht erlaubt, da Sie nicht angemeldet sind.';

				return;
			}

			if(!$this->User->isMemberOf(Config::get('fernschach_memberFernschach')))
			{
				$this->Template->fehlertext = 'Zugriff auf das Formular nicht erlaubt, da kein verifiziertes BdF-Mitglied.';

				return;
			}
		}

		$mitglied = Helper::getSpielerdatensatz(FrontendUser::getInstance()->fernschach_memberId);

		if(!$mitglied || !$mitglied->numRows)
		{
			$this->Template->fehlertext = 'Ihrem Benutzerkonto ist kein BdF-Mitglied zugeordnet. Bitte wenden Sie sich an die Geschäftsstelle.';

			return;
		}

		$this->Template->mitglied = $mitglied;

		// Rückkehr von der Umleitung nach dem Absenden
		if(Input::get('send'))
		{
			$this->Template->bestaetigung = Helper::getAnmeldungenBewerbungen($mitglied->id);

			return;
		}

		// Kontostände und SEPA-Lage
		$beitragssaldo = Helper::getBeitragssaldo($mitglied->id);
		$nenngeldsaldo = Helper::getNenngeldsaldo($mitglied->id);
		$hauptsalden = Helper::getSaldo($mitglied->id, '', false, false);
		$hauptsaldo = (float) (end($hauptsalden) ?: 0);

		$this->Template->konten = array
		(
			'haupt'    => array('wert' => $hauptsaldo, 'text' => self::formatBetrag($hauptsaldo), 'zeigen' => 0.0 != $hauptsaldo),
			'beitrag'  => array('wert' => (float) $beitragssaldo, 'text' => self::formatBetrag($beitragssaldo), 'zeigen' => true),
			'nenngeld' => array('wert' => $nenngeldsaldo, 'text' => self::formatBetrag($nenngeldsaldo), 'zeigen' => true),
		);

		$this->Template->sepa = array
		(
			'beitrag'  => (bool) $mitglied->sepaBeitrag,
			'nenngeld' => (bool) $mitglied->sepaNenngeld,
		);

		$this->Template->qualifikationen = Helper::getQualifikationen($mitglied) ?: array();
		$this->Template->meldungen = self::letzteMeldungen($mitglied, 5);

		// Ohne geregelten Beitrag ist keine Meldung möglich — das ist die erste
		// Bedingung aus der Ablaufbeschreibung und hat Vorrang vor allem anderen.
		if(!Helper::beitragGedeckt($mitglied))
		{
			$this->Template->fehlertext = 'Eine '.$this->Template->begriff.' ist zurzeit nicht möglich: Ihr Beitragskonto weist '.self::formatBetrag($beitragssaldo).' aus und es liegt keine SEPA-Vereinbarung für den Beitrag vor. Bitte gleichen Sie Ihr Beitragskonto aus oder erteilen Sie der Geschäftsstelle eine SEPA-Vereinbarung.';

			return;
		}

		$turniere = self::getTournaments($mitglied, $nenngeldsaldo);
		$this->Template->turniere = $turniere;

		if(!$turniere)
		{
			$this->Template->fehlertext = $mitglied->sepaNenngeld
				? 'Zurzeit steht kein Turnier zur '.$this->Template->begriff.' offen.'
				: 'Zurzeit steht kein Turnier zur '.$this->Template->begriff.' offen. Möglich ist auch, dass das Guthaben auf Ihrem Nenngeldkonto ('.self::formatBetrag($nenngeldsaldo).') für keines der offenen Turniere reicht und keine SEPA-Vereinbarung für das Nenngeld vorliegt.';

			return;
		}

		$werte = array
		(
			'turnier'       => (int) Input::post('turnier'),
			'qualifikation' => trim((string) Input::post('qualifikation')),
			'bemerkungen'   => trim((string) Input::post('bemerkungen')),
		);

		$this->Template->werte = $werte;

		if(Input::post('FORM_SUBMIT') !== 'fernschach_turnieranmeldung')
		{
			return;
		}

		if(!$werte['turnier'] || !self::turnierErlaubt($turniere, $werte['turnier']))
		{
			$this->Template->fehler = array('turnier' => 'Bitte wählen Sie ein Turnier aus der Liste aus.');

			return;
		}

		if(!self::saveMeldung($werte))
		{
			$this->Template->fehler = array('turnier' => $this->fernschachverwaltung_bewerbung
				? 'Ihre Bewerbung konnte nicht gespeichert werden. Möglicherweise haben Sie sich für dieses Turnier bereits beworben.'
				: 'Ihre Anmeldung konnte nicht gespeichert werden. Möglicherweise sind Sie für dieses Turnier bereits gemeldet.');

			return;
		}

		// Nach dem Speichern umleiten statt neu laden. Erst dadurch entsteht ein
		// GET-Aufruf, bei dem der Browser nicht mehr nach dem erneuten Absenden
		// der Formulardaten fragt — genau daraus sind Doppelmeldungen entstanden.
		Controller::redirect(Controller::addToUrl('send=1'));
	}

	/**
	 * Prüft, ob eine Turniernummer in der angebotenen Auswahl vorkommt.
	 *
	 * Die Auswahl ist nach Turnierkategorien gruppiert; gesucht wird deshalb in
	 * allen Gruppen.
	 *
	 * @param array $turniere Die Auswahl aus getTournaments()
	 * @param int   $id       Die abgeschickte Turniernummer
	 *
	 * @return bool True, wenn das Turnier angeboten wurde
	 */
	protected function turnierErlaubt($turniere, $id)
	{
		foreach($turniere as $gruppe)
		{
			if(isset($gruppe[$id]))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Liefert die letzten Meldungen eines Spielers.
	 *
	 * @param object $mitglied Spielerdatensatz
	 * @param int    $anzahl   Höchstzahl der zurückgegebenen Einträge
	 *
	 * @return array Liste mit den Schlüsseln 'datum', 'typ' und 'turnier'
	 */
	protected function letzteMeldungen($mitglied, $anzahl)
	{
		$meldungen = Helper::getAnmeldungenBewerbungen($mitglied->id);

		return \is_array($meldungen) ? \array_slice($meldungen, 0, $anzahl) : array();
	}

	/**
	 * Formatiert einen Geldbetrag deutsch mit Euro-Zeichen.
	 *
	 * @param float|int|string $betrag Der Betrag in Euro
	 *
	 * @return string Der Betrag als „1.234,50 €"
	 */
	protected static function formatBetrag($betrag)
	{
		return number_format((float) $betrag, 2, ',', '.').' €';
	}


	/**
	 * Speichert eine Anmeldung oder Bewerbung und verschickt die E-Mails.
	 *
	 * Die Turnierauswahl im Formular enthält bereits nur zulässige Turniere.
	 * Hier wird trotzdem noch einmal geprüft: Ein zweiter Browsertab, der
	 * Zurück-Knopf oder ein doppelter Klick auf „Absenden" schicken sonst
	 * dieselbe Meldung ein zweites Mal ab.
	 *
	 * @param array $data Die abgeschickten Formularwerte, mindestens mit den
	 *                    Schlüsseln 'turnier', 'qualifikation' und 'bemerkungen'
	 *
	 * @return bool True, wenn die Meldung gespeichert wurde. False, wenn kein
	 *              Turnier gewählt wurde, es das Turnier nicht (mehr) gibt oder
	 *              der Spieler die zulässige Zahl an Meldungen bereits erreicht
	 *              hat — in dem Fall wird nichts gespeichert und nichts
	 *              verschickt
	 */
	protected function saveMeldung($data)
	{
		$zeit = time();

		// Mitgliedsdaten laden
		$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpielerdatensatz(FrontendUser::getInstance()->fernschach_memberId);

		if(!$mitglied || !$mitglied->numRows || empty($data['turnier']))
		{
			return false;
		}

		// Turnier laden und die Zahl der erlaubten Meldungen prüfen
		$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($data['turnier']);

		if(!$objTurnier || !$objTurnier->numRows)
		{
			return false;
		}

		if(!\Schachbulle\ContaoFernschachBundle\Classes\Helper::meldungErlaubt($objTurnier, $mitglied->id, (bool) $this->fernschachverwaltung_bewerbung))
		{
			Scope::log(
				'[Fernschach-Verwaltung] Mehrfachmeldung abgewiesen: Spieler '.$mitglied->nachname.', '.$mitglied->vorname.' (ID '.$mitglied->id.') für Turnier '.$objTurnier->title.' (ID '.$objTurnier->id.')',
				__METHOD__,
				ContaoContext::GENERAL
			);

			return false;
		}

		// Turnier prüfen
		if($data['turnier'])
		{
			if($this->fernschachverwaltung_bewerbung)
			{
				// Bewerbung wurde gesendet
				$set = array
				(
					'pid'               => $data['turnier'],
					'tstamp'            => $zeit,
					'vorname'           => $mitglied->vorname,
					'nachname'          => $mitglied->nachname,
					'applicationDate'   => $zeit, // Bewerbungsdatum
					'spielerId'         => $mitglied->id,
					'infoQualifikation' => $data['qualifikation'],
					'bemerkungen'       => $data['bemerkungen'],
					'published'         => 1
				);
				$objInsert = Database::getInstance()->prepare('INSERT INTO tl_fernschach_turniere_bewerbungen %s')
				                                     ->set($set)
				                                     ->execute();
				$meldungId = $objInsert->insertId;
			}
			else
			{
				// Anmeldung wurde gesendet
				// Meldung erzeugen
				$set = array
				(
					'pid'               => $data['turnier'],
					'tstamp'            => $zeit,
					'spielerId'         => $mitglied->id,
					'vorname'           => $mitglied->vorname,
					'nachname'          => $mitglied->nachname,
					'plz'               => $mitglied->plz,
					'ort'               => $mitglied->ort,
					'strasse'           => $mitglied->strasse,
					'email'             => $mitglied->email1,
					'fax'               => $mitglied->fax ? $mitglied->fax : '',
					'memberId'          => $mitglied->memberId,
					'meldungDatum'      => $zeit,
					'infoQualifikation' => $data['qualifikation'],
					'bemerkungen'       => $data['bemerkungen'],
					'published'         => 1
				);
				$objInsert = Database::getInstance()->prepare('INSERT INTO tl_fernschach_turniere_meldungen %s')
				                                     ->set($set)
				                                     ->execute();
				$meldungId = $objInsert->insertId;

				// Nenngeldbuchung Soll erzeugen
				$set = array
				(
					'pid'               => $mitglied->id,
					'tstamp'            => $zeit,
					'betrag'            => $objTurnier->nenngeld,
					'typ'               => 's',
					'datum'             => $zeit,
					'kategorie'         => 's',
					'art'               => 'n',
					'verwendungszweck'  => 'Nenngeld-Forderung '.$objTurnier->title,
					'turnier'           => $data['turnier'],
					'comment'           => 'Datensatz erzeugt durch Turnieranmeldung am '.date('d.m.Y H:i', $zeit),
					'meldungId'         => $meldungId,
					'published'         => '1'
				);
				$objInsert = Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler_konto_nenngeld %s')
				                                     ->set($set)
				                                     ->execute(); 
			}
		}

		// E-Mail für Turnierleiter zusammenbauen
		$turnierleiter = \Schachbulle\ContaoFernschachBundle\Classes\Turnier::getTurnierleiter($data['turnier']); // Übergeben wird die ID des Turniers

		if(isset($turnierleiter[0]))
		{
			// Email verschicken
			$objEmail = new Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = Config::get('fernschach_emailAdresse');
			$objEmail->fromName = Config::get('fernschach_emailVon');
			$objEmail->sendBcc(Config::get('fernschach_emailVon').' <'.Config::get('fernschach_emailAdresse').'>');
			if($this->fernschachverwaltung_bewerbung) $objEmail->subject = 'Turnierbewerbung '.$objTurnier->title;
			else $objEmail->subject = 'Turnieranmeldung '.$objTurnier->title;
			$objEmail->replyTo($turnierleiter[0]['name'].' <'.$turnierleiter[0]['email'].'>');
			// Weitere Empfänger einbauen
			if(count($turnierleiter) > 1)
			{
				$empfaenger = array();
				for($x = 1; $x < count($turnierleiter); $x++)
				{
					if($turnierleiter[$x]['email']) $empfaenger[] = $turnierleiter[$x]['name'] . ' <' . $turnierleiter[$x]['email'] . '>';
				}
				$cc = implode(',', $empfaenger);
				$objEmail->sendCc($cc);
			}
			// Backend-Link zum Turnier generieren
			$backendlink = Scope::replaceInsertTags('{{env::url}}').'/contao?do=fernschach-turniere&table=tl_fernschach_turniere_meldungen&rt='.Scope::getRequestToken().'&id='.$objTurnier->id;
			// Kommentar zusammenbauen
			$text = '<html><head><title></title></head><body>';
			if($this->fernschachverwaltung_bewerbung) $text .= '<p>Eine neue Turnierbewerbung wurde abgegeben:</p>';
			else $text .= '<p>Eine neue Turnieranmeldung wurde vorgenommen:</p>';
			$text .= '<h3>Angaben zum Turnier</h3>';
			$text .= '<ul>';
			$text .= '<li>Meldezeit: <b>'.date('d.m.Y H:i', $zeit).'</b></li>';
			$text .= '<li>Turnier: <b>'.$objTurnier->title.'</b> (<a href="'.$backendlink.'" target="_blank">Bearbeiten</a>)</li>';
			$text .= '<li>Meldeschluss: <b>'.($objTurnier->registrationDate ? date('d.m.Y', $objTurnier->registrationDate) : '-').'</b></li>';
			$text .= '<li>Nenngeld: <b>'.str_replace('.', ',', sprintf('%0.2f',$objTurnier->nenngeld)).'</b></li>';
			$text .= '</ul>';
			$text .= '<h3>Angaben zum Spieler</h3>';
			$text .= '<ul>';
			$text .= '<li>Vor- und Nachname: <b>'.$mitglied->vorname.' '.$mitglied->nachname.'</b></li>';
			$text .= '<li>BdF-Mitgliedsnummer: <b>'.$mitglied->memberId.'</b></li>';
			$text .= '<li>ICCF-Mitgliedsnummer: <b>'.$mitglied->memberInternationalId.'</b></li>';
			$text .= '<li>Adresse: <b>'.$mitglied->plz.' '.$mitglied->ort.', '.$mitglied->strasse.'</b></li>';
			$text .= '<li>Fax: <b>'.$mitglied->fax.'</b></li>';
			$text .= '<li>E-Mail: <b>'.$mitglied->email1.'</b></li>';
			$text .= '</ul>';
			$text .= '<h3>Sonstiges</h3>';
			$text .= '<ul>';
			$text .= '<li>Information zur Qualifikation: <b>'.$data['qualifikation'].'</b></li>';
			$text .= '<li>Bemerkungen: <b>'.$data['bemerkungen'].'</b></li>';
			$text .= '</ul>';
			$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';
			//	'pid'               => $data['turnier'],
			//	'tstamp'            => $zeit,
			//	'spielerId'         => $spielerId,

			// Add the comment details
			$objEmail->html = $text;
			$objEmail->sendTo(array($turnierleiter[0]['name'].' <'.$turnierleiter[0]['email'].'>'));
		}

		// E-Mail für Anmelder erstellen
		if(isset($mitglied->email1))
		{
			// Email verschicken
			$objEmail = new Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = Config::get('fernschach_emailAdresse');
			$objEmail->fromName = Config::get('fernschach_emailVon');
			if($this->fernschachverwaltung_bewerbung) $objEmail->subject = 'Turnierbewerbung '.$objTurnier->title;
			else $objEmail->subject = 'Turnieranmeldung '.$objTurnier->title;
			// Kommentar zusammenbauen
			$text = '<html><head><title></title></head><body>';
			if($this->fernschachverwaltung_bewerbung) $text .= '<p>Sie haben eine Turnierbewerbung abgegeben:</p>';
			else $text .= '<p>Sie haben eine Turnieranmeldung vorgenommen:</p>';
			$text .= '<h3>Angaben zum Turnier</h3>';
			$text .= '<ul>';
			$text .= '<li>Meldezeit: <b>'.date('d.m.Y H:i', $zeit).'</b></li>';
			$text .= '<li>Turnier: <b>'.$objTurnier->title.'</b></li>';
			$text .= '<li>Meldeschluss: <b>'.($objTurnier->registrationDate ? date('d.m.Y', $objTurnier->registrationDate) : '-').'</b></li>';
			$text .= '<li>Nenngeld: <b>'.str_replace('.', ',', sprintf('%0.2f',$objTurnier->nenngeld)).'</b></li>';
			$text .= '</ul>';
			$text .= '<h3>Angaben zum Spieler</h3>';
			$text .= '<ul>';
			$text .= '<li>Vor- und Nachname: <b>'.$mitglied->vorname.' '.$mitglied->nachname.'</b></li>';
			$text .= '<li>BdF-Mitgliedsnummer: <b>'.$mitglied->memberId.'</b></li>';
			$text .= '<li>ICCF-Mitgliedsnummer: <b>'.$mitglied->memberInternationalId.'</b></li>';
			$text .= '<li>Adresse: <b>'.$mitglied->plz.' '.$mitglied->ort.', '.$mitglied->strasse.'</b></li>';
			$text .= '<li>Fax: <b>'.$mitglied->fax.'</b></li>';
			$text .= '<li>E-Mail: <b>'.$mitglied->email1.'</b></li>';
			$text .= '</ul>';
			$text .= '<h3>Sonstiges</h3>';
			$text .= '<ul>';
			$text .= '<li>Information zur Qualifikation: <b>'.$data['qualifikation'].'</b></li>';
			$text .= '<li>Bemerkungen: <b>'.$data['bemerkungen'].'</b></li>';
			$text .= '</ul>';
			$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';
			//	'pid'               => $data['turnier'],
			//	'tstamp'            => $zeit,
			//	'spielerId'         => $spielerId,

			// Add the comment details
			$objEmail->html = $text;
			$objEmail->sendTo(array($mitglied->vorname.' '.$mitglied->nachname.' <'.$mitglied->email1.'>'));
		}

		return true;
	}

	/**
	 * Funktion getTournaments
	 * =======================
	 * Turniere einlesen: veröffentlicht, Online-Anmeldung aktiv, ohne Meldedatum oder Meldedatum kleiner akt. Datum
	 *
	 * param $sepa        Boolean    Status des SEPA-Mandats für Nenngeld
	 * param $saldo       Float      Saldo des Nenngeldkontos
	 * param $klasse      String     M, H, O oder leer (Klasse des Spielers)
	 *
	 * @return array
	 */
	public function getTournaments($mitglied, $saldo)
	{
		$Turniere = array();
		$Standardgruppe = 'Weitere Turniere'; // Name des optgroup-Labels für nichtzugeordnete Turniere
		$zeit = time();
		$monat = date('m', $zeit);
		$tag = date('d', $zeit);
		$jahr = date('Y', $zeit);
		$aktuellesDatum = mktime(0, 0, 0, $monat, $tag, $jahr);

		// Meldefähige Turniere laden
		if($this->fernschachverwaltung_bewerbung)
		{
			$objTurniere = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE (registrationDate >= ? OR registrationDate = ?) AND onlineAnmeldung = ? AND bewerbungErlaubt = ? AND typ != ? AND published = ? ORDER BY art ASC, title ASC")
			                                       ->execute($aktuellesDatum, 0, 1, 1, 'm', 1);
		}
		else
		{
			$objTurniere = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE (registrationDate >= ? OR registrationDate = ?) AND onlineAnmeldung = ? AND typ != ? AND published = ? ORDER BY art ASC, title ASC")
			                                       ->execute($aktuellesDatum, 0, 1, 'm', 1);
		}

		while($objTurniere->next())
		{
			$published = self::TurnierkategorieVeroeffentlicht($objTurniere->pid); // Prüfen, ob alle übergeordneten Turnierkategorien veröffentlicht sind
			$Gruppenname = self::Turniergruppe($objTurniere->pid); // Titel der Turnierkategorie laden

			if($published)
			{
				$turnieranmeldung = true;

				// ==================================================
				// Spielermaximum prüfen
				// ==================================================
				if($objTurniere->spielerMax > 0)
				{
					// Ein Spielermaximum ist gesetzt, jetzt prüfen ob noch Anmeldungen möglich sind
					$objMeldungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE pid = ? AND published = ?")
					                                        ->execute($objTurniere->id, 1);
					if($objMeldungen->numRows >= $objTurniere->spielerMax)
					{
						$turnieranmeldung = false; // Anmeldung für dieses Turnier überspringen, da maximale Spielerzahl erreicht ist
					}
				}

				// ==================================================
				// Klasse des Spielers prüfen
				// ==================================================
				if($mitglied->klassenberechtigung == '')
				{
					// Keine Klasse gesetzt, nur offene Klasse möglich
					if($objTurniere->klassenzuordnung != '' && ($objTurniere->klassenzuordnung == 'M' || $objTurniere->klassenzuordnung == 'H'))
					{
						$turnieranmeldung = false; // Anmeldung für dieses Turnier überspringen, da Klassenberechtigung nicht übereinstimmt
					}
				}
				else
				{
					// Eine Klassenberechtigung ist gesetzt
					if($objTurniere->klassenzuordnung != '' && $objTurniere->klassenzuordnung != $mitglied->klassenberechtigung)
					{
						$turnieranmeldung = false; // Anmeldung für dieses Turnier überspringen, da Klassenberechtigung nicht übereinstimmt
					}
				}

				// ==================================================
				// Geschlechtsbeschränkung prüfen
				// ==================================================
				if($objTurniere->spielerGeschlecht != '' && $objTurniere->spielerGeschlecht != $mitglied->sex)
				{
					// Geschlecht des Spielers für dieses Turnier nicht zugelassen
					$turnieranmeldung = false;
				}
				
				// ==================================================
				// Mindestalter prüfen
				// ==================================================
				if($objTurniere->spielerAlterMin > 0)
				{
					$aktuell = (int)date('Y').'1231';
					$alter = ($aktuell - $mitglied->birthday) / 10000;
					if($objTurniere->spielerAlterMin >= $alter)
					{
						// Spieler zu jung für dieses Turnier, deshalb nicht zugelassen
						$turnieranmeldung = false;
					}
				}
				
				// ==================================================
				// Maximalalter prüfen
				// ==================================================
				if($objTurniere->spielerAlterMax > 0)
				{
					$aktuell = (int)date('Y').'0101';
					$alter = ($aktuell - $mitglied->birthday) / 10000;
					if($objTurniere->spielerAlterMax <= $alter)
					{
						// Spieler zu alt für dieses Turnier, deshalb nicht zugelassen
						$turnieranmeldung = false;
					}
				}
				
				// ==================================================
				// Bereits vorhandene Meldungen prüfen
				// ==================================================
				// Am Turnier steht, wie oft sich derselbe Spieler melden darf
				// (0 = unbegrenzt). Ist die Zahl erreicht, taucht das Turnier
				// gar nicht erst in der Auswahl auf — das ist der wirksamste
				// Schutz gegen die Mehrfachbewerbungen, die entstehen, wenn
				// jemand das Formular zweimal abschickt.
				if(!\Schachbulle\ContaoFernschachBundle\Classes\Helper::meldungErlaubt($objTurniere, $mitglied->id, (bool) $this->fernschachverwaltung_bewerbung))
				{
					$turnieranmeldung = false;
				}

				// Turnier in die Auswahl eintragen, wenn erlaubt
				if($turnieranmeldung && $Gruppenname)
				{
					// Der Vergleich lief früher über (int) auf das Nenngeld und
					// hat die Nachkommastellen abgeschnitten: Aus 25,50 wurde 25,
					// und mit 25,49 auf dem Konto war die Meldung möglich.
					// Helper::nenngeldGedeckt() rechnet in Cent.
					if(!Helper::nenngeldGedeckt($mitglied, $objTurniere->nenngeld, $saldo))
					{
						continue;
					}

					$Gruppe = $Gruppenname ? $Gruppenname : $Standardgruppe;

					if(!isset($Turniere[$Gruppe]))
					{
						$Turniere[$Gruppe] = array();
					}

					$Turniere[$Gruppe][$objTurniere->id] = array
					(
						'id'           => (int) $objTurniere->id,
						'title'        => $objTurniere->title,
						'nenngeld'     => self::formatBetrag($objTurniere->nenngeld),
						'meldeschluss' => $objTurniere->registrationDate ? date('d.m.Y', (int) $objTurniere->registrationDate) : '',
					);
				}
			}
		}

		return $Turniere;
	}

	/*
	 * Funktion TurnierkategorieVeroeffentlicht
	 * Liefert true/false, je nach veröffentlichten Oberkategorien
	 */
	private function TurnierkategorieVeroeffentlicht($id)
	{
		while($id > 0)
		{
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);

			if($objTurnier->published)
			{
				$id = $objTurnier->pid; // Neue ID setzen
			}
			else
			{
				return false; // Kategorie ist nicht veröffentlicht
			}
		}
		return true;
	}

	/*
	 * Funktion TurnierkategorieVeroeffentlicht
	 * Liefert true/false, je nach veröffentlichten Oberkategorien
	 */
	private function Turniergruppe($id)
	{
		$gruppe = '';
		$gruppeID = 0;

		while($id > 0)
		{
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);

			// Gruppenname ermitteln
			if($objTurnier->titleView)
			{
				$gruppe = $objTurnier->titleAlternate ? $objTurnier->titleAlternate : $objTurnier->title;
				$gruppeID = $id;
			}
			$id = $objTurnier->pid; // Neue ID setzen
		}

		// Prüfen, ob eine Turniergruppe als Root festgelegt ist
		if($this->fernschachverwaltung_tournamentRoot)
		{
			// Ja, es wurde eine Rootgruppe festgelegt
			if($this->fernschachverwaltung_tournamentRoot == $gruppeID)
			{
				// Richtige Gruppe
				return $gruppe;
			}
		}
		else
		{
			// Nein, es wurde keine Rootgruppe festgelegt
			return $gruppe;
		}
		return false;
	}

	// Hinweis zur Version 2.0.0: An dieser Stelle stand eine Methode
	// sendNotification(), die nie aufgerufen wurde und beim ersten Aufruf
	// abgestürzt wäre — sie griff auf CalendarModel und Notification zu, also
	// auf Klassen, die dieses Bundle gar nicht voraussetzt. Sie ist entfallen.

	/*
	 * Funktion DatumToZeitstempel
	 * Datum TT.MM.JJJJ in Unix-Timestamp umwandeln
	 */
	private function DatumToZeitstempel($string)
	{
		$tag = substr($string, 0, 2);
		$monat = substr($string, 3, 2);
		$jahr = substr($string, 6, 4);
		return mktime(0, 0, 0, $monat, $tag, $jahr);
	}

}
