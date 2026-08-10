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
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

class Meldeformular_Mannschaft extends Module
{

	protected $strTemplate = 'mod_fernschach';

	/**
	 * Display a wildcard in the back end
	 * @return string
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

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;
		$fehler = false;

		if($this->fernschachverwaltung_linkingMembers)
		{
			// Das Formular darf nur BdF-Mitgliedern angezeigt werden
			// Jetzt auf BdF-Mitglied prüfen
			$this->import(FrontendUser::class,'User'); // Frontend-Mitglied laden
			if($this->User)
			{
				if(!$this->User->isMemberOf(Config::get('fernschach_memberFernschach')))
				{
					// Frontend-Mitglied gehört nicht zur Gruppe BdF-Mitglied
					$fehler = true;
					$fehlertext = 'Zugriff auf das Formular nicht erlaubt, da kein verifiziertes BdF-Mitglied.';
				}
			}
			else
			{
				// Nicht im Frontend angemeldet
				$fehler = true;
				$fehlertext = 'Zugriff auf das Formular nicht erlaubt, da nicht angemeldet.';
			}
		}

		if($fehler)
		{
			echo $fehlertext;
		}
		else
		{
			// Template füllen
			$this->Template->daten = self::Formular();
		}
	}

	protected function Formular()
	{

		// BdF-Mitgliedsdaten des angemeldeten Benutzers laden
		$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpielerdatensatz(FrontendUser::getInstance()->fernschach_memberId);

		// Ohne zugeordneten Spielerdatensatz gibt es nichts anzumelden. Vor der
		// Version 2.0.0 lief die Methode trotzdem weiter und erzeugte unter
		// PHP 8 für jedes Feld eine Warnung.
		if(!$mitglied || !$mitglied->numRows)
		{
			return '<p class="error">Ihrem Benutzerkonto ist kein BdF-Mitglied zugeordnet. Bitte wenden Sie sich an die Geschäftsstelle.</p>';
		}

		// Rückkehr von der Umleitung nach dem Absenden: Bestätigung statt Formular
		if(Input::get('send'))
		{
			return '<div class="fernschach-bestaetigung">'
				.'<p class="confirmation"><b>Ihre Mannschaftsmeldung ist eingegangen.</b></p>'
				.'<p>Sie erhalten zusätzlich eine Bestätigung per E-Mail an <b>'.$mitglied->email1.'</b>. '
				.'Bitte sehen Sie auch im Spam-Ordner nach. Eine erneute Meldung für dasselbe Turnier ist nicht nötig.</p>'
				.'</div>';
		}

		$beitragssaldo = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getBeitragssaldo(FrontendUser::getInstance()->fernschach_memberId);

		$records = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE published = ? AND archived = ? ORDER BY nachname ASC, vorname ASC")
		                                   ->execute('1', '');
		$mitglieder = array();
		if($records->numRows)
		{
			while($records->next())
			{
				$bdf_mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($records);
				if($bdf_mitglied)
				{
					// Nur Mitglieder berücksichtigen
					$saldo_beitrag = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getBeitragssaldo($records->id);
					if($records->sepaBeitrag)
					{
						// SEPA-Mandat vorhanden, Spieler berücksichtigen
						$datensatz = $records->nachname.','.$records->vorname.' (BdF-Nr. '.$records->memberId.' / ICCF-ID '.$records->memberInternationalId.')';
						$mitglieder[$datensatz] = $datensatz;
					}
					else
					{
						// Kein SEPA-Mandat
						if($saldo_beitrag >= 0)
						{
							// Nur Nichtrückstand bei Beitrag berücksichtigen
							$datensatz = $records->nachname.','.$records->vorname.' (BdF-Nr. '.$records->memberId.' / ICCF-ID '.$records->memberInternationalId.')';
							$mitglieder[$datensatz] = $datensatz;
						}
					}
				}
			}
		}

		$mitgliedsdaten = '<h4>Angemeldeter Benutzer</h4>';
		$mitgliedsdaten .= '<ul>';
		$mitgliedsdaten .= '<li>Anmeldename: <b>'.FrontendUser::getInstance()->username.'</b></li>';
		$mitgliedsdaten .= '<li>Vor- und Nachname: <b>'.FrontendUser::getInstance()->firstname.' '.FrontendUser::getInstance()->lastname.'</b></li>';
		$mitgliedsdaten .= '<li>E-Mail-Adresse: <b>'.FrontendUser::getInstance()->email.'</b></li>';
		$mitgliedsdaten .= '</ul>';
		$mitgliedsdaten .= '<h4>Zugeordnetes BdF-Mitglied</h4>';
		$mitgliedsdaten .= '<ul>';
		$mitgliedsdaten .= '<li>Vor- und Nachname: <b>'.$mitglied->vorname.' '.$mitglied->nachname.'</b></li>';
		$mitgliedsdaten .= '<li>Mitgliedsnummer: <b>'.$mitglied->memberId.'</b></li>';
		$mitgliedsdaten .= '<li>E-Mail-Adresse 1: <b>'.$mitglied->email1.'</b></li>';
		$mitgliedsdaten .= '<li>E-Mail-Adresse 2: <b>'.$mitglied->email2.'</b></li>';
		$mitgliedsdaten .= '<li>Saldo Beitragskonto: <b>'.$beitragssaldo.'</b></li>';
		$mitgliedsdaten .= '</ul>';
		$mitgliedsdaten .= '<p><b>Du bist der Mannschaftsführer der zu meldenden Mannschaft. Das Nenngeld des ausgewählten Turniers wird mit Deinem Nenngeldkonto verrechnet.</b></p>';
		
		$form = new \Schachbulle\ContaoHelperBundle\Classes\Form();
		$form->addField(array('typ' => 'hidden', 'name' => 'FORM_SUBMIT', 'value' => 'form_turnieranmeldung'));
		$form->addField(array('typ' => 'hidden', 'name' => 'REQUEST_TOKEN', 'value' => Scope::getRequestToken()));
		$form->addField(array('typ' => 'hidden', 'name' => 'registrierung', 'value' => time()));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Mannschaftsführer'));
		$form->addField(array('typ' => 'explanation', 'label' => $mitgliedsdaten));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Turnier'));
		$form->addField(array('typ' => 'select', 'name' => 'turnier', 'mandatory' => true, 'options' => self::getTournaments($mitglied, $beitragssaldo)));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Angaben zur Mannschaft'));
		$form->addField(array('typ' => 'text', 'name' => 'vereinsname', 'label' => 'Genauer Name des Vereins bzw. der Spielgemeinschaft', 'mandatory' => true));
		$form->addField(array('typ' => 'text', 'name' => 'vereinsname_alt', 'label' => 'Alter Name der Mannschaft (bei Namensänderung)'));
		$form->addField(array('typ' => 'text', 'name' => 'mannschaftsname', 'label' => 'Genaue Bezeichnung der gemeldeten Mannschaft (ggf. mit römischer Ziffer bei mehreren Teams)', 'mandatory' => true));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Spieler'));
		$form->addField(array('typ' => 'select', 'name' => 'brett1', 'label' => 'Brett 1', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'select', 'name' => 'brett2', 'label' => 'Brett 2', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'select', 'name' => 'brett3', 'label' => 'Brett 3', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'select', 'name' => 'brett4', 'label' => 'Brett 4', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Sonstiges'));
		$form->addField(array('typ' => 'textarea', 'name' => 'bemerkungen', 'label' => 'Bemerkungen'));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'submit', 'label' => 'Anmeldung absenden'));
		$fehler = '';

		// validate() prüft auch, ob das Formular gesendet wurde
		if($form->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $form->fetchAll();

			if(self::saveMeldung($arrData))
			{
				// Nach dem Speichern umleiten statt neu laden — sonst fragt der
				// Browser beim Aktualisieren nach dem erneuten Absenden und legt
				// eine zweite Nenngeld-Sollbuchung an.
				Controller::redirect(Controller::addToUrl('send=1'));
			}

			$fehler = '<p class="error">Ihre Meldung konnte nicht gespeichert werden. Möglicherweise haben Sie für dieses Turnier bereits eine Mannschaft gemeldet.</p>';
		}

		// Formular als String zurückgeben
		return $fehler.$form->generate();

	}

	/**
	 * Speichert eine Mannschaftsmeldung und verschickt die E-Mails.
	 *
	 * Gespeichert wird ausschließlich die Nenngeld-Sollbuchung auf dem Konto des
	 * Mannschaftsführers; die eigentliche Mannschaftsaufstellung geht per E-Mail
	 * an den Turnierdirektor.
	 *
	 * @param array $data Die abgeschickten Formularwerte, mindestens mit dem
	 *                    Schlüssel 'turnier'
	 *
	 * @return bool True, wenn gespeichert wurde. False, wenn kein Turnier
	 *              gewählt wurde, es das Turnier nicht (mehr) gibt oder für
	 *              dieses Turnier bereits gemeldet wurde
	 */
	protected function saveMeldung($data)
	{
		// BdF-Mitgliedsdaten des angemeldeten Benutzers laden
		$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpielerdatensatz(FrontendUser::getInstance()->fernschach_memberId);

		if(!$mitglied || !$mitglied->numRows || empty($data['turnier']))
		{
			return false;
		}

		$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($data['turnier']);

		if(!$objTurnier || !$objTurnier->numRows)
		{
			return false;
		}

		// Zweite Prüfung nach dem Absenden: Der Browser kann dieselbe Meldung
		// über den Zurück-Knopf oder einen zweiten Tab erneut schicken.
		if(self::bereitsGemeldet($objTurnier, $mitglied->id))
		{
			Scope::log(
				'[Fernschach-Verwaltung] Mehrfache Mannschaftsmeldung abgewiesen: '.$mitglied->nachname.', '.$mitglied->vorname.' (ID '.$mitglied->id.') für Turnier '.$objTurnier->title.' (ID '.$objTurnier->id.')',
				__METHOD__,
				ContaoContext::GENERAL
			);

			return false;
		}

		// Nenngeldbuchung Soll erzeugen
		$zeit = time();
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
			'meldungId'         => 0,
			'published'         => '1'
		);
		$objInsert = Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler_konto_nenngeld %s')
		                                     ->set($set)
		                                     ->execute(); 

		// Text für Absender zusammenbauen
		$text = '<html><head><title></title></head><body>';
		$text .= '<p>Sie haben eine Meldung zu einer Mannschaftsmeisterschaft vorgenommen:</p>';
		$text .= '<h3>Angaben zum Turnier</h3>';
		$text .= '<ul>';
		$text .= '<li>Turniername: <b>'.$objTurnier->title.'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Angaben zur Mannschaft</h3>';
		$text .= '<ul>';
		$text .= '<li>Vereinsname: <b>'.$data['vereinsname'].'</b></li>';
		$text .= '<li>Alter Vereinsname: <b>'.$data['vereinsname_alt'].'</b></li>';
		$text .= '<li>Mannschaftsname: <b>'.$data['mannschaftsname'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Mannschaftsführer</h3>';
		$text .= '<ul>';
		$text .= '<li>BdF-Mitgliedsnummer: <b>'.$mitglied->memberId.'</b></li>';
		$text .= '<li>ICCF-ID: <b>'.$mitglied->memberInternationalId.'</b></li>';
		$text .= '<li>Vorname: <b>'.$mitglied->vorname.'</b></li>';
		$text .= '<li>Nachname: <b>'.$mitglied->nachname.'</b></li>';
		$text .= '<li>E-Mail: <b>'.$mitglied->email1.'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Spieler</h3>';
		$text .= '<ul>';
		$text .= '<li>Brett 1: <b>'.$data['brett1'].'</b></li>';
		$text .= '<li>Brett 2: <b>'.$data['brett2'].'</b></li>';
		$text .= '<li>Brett 3: <b>'.$data['brett3'].'</b></li>';
		$text .= '<li>Brett 4: <b>'.$data['brett4'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Sonstiges</h3>';
		$text .= '<ul>';
		$text .= '<li>Bemerkungen: <b>'.$data['bemerkungen'].'</b></li>';
		$text .= '</ul>';
		$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';

		// Email an Absender verschicken
		$objEmail = new Email();
		$objEmail->charset = 'utf-8';
		$objEmail->from = Config::get('fernschach_emailAdresse');
		$objEmail->fromName = Config::get('fernschach_emailVon');
		$objEmail->subject = 'Mannschaftsmeldung '.$data['vereinsname'];
		$objEmail->html = $text;
		$objEmail->sendTo(array($mitglied->vorname.' '.$mitglied->nachname.' <'.$mitglied->email1.'>'));

		// Text für Turnierdirektor zusammenbauen
		$text = '<html><head><title></title></head><body>';
		$text .= '<p>Eine neue Turnieranmeldung wurde vorgenommen:</p>';
		$text .= '<h3>Angaben zum Turnier</h3>';
		$text .= '<ul>';
		$text .= '<li>Turniername: <b>'.$objTurnier->title.'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Angaben zur Mannschaft</h3>';
		$text .= '<ul>';
		$text .= '<li>Vereinsname: <b>'.$data['vereinsname'].'</b></li>';
		$text .= '<li>Alter Vereinsname: <b>'.$data['vereinsname_alt'].'</b></li>';
		$text .= '<li>Mannschaftsname: <b>'.$data['mannschaftsname'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Mannschaftsführer</h3>';
		$text .= '<ul>';
		$text .= '<li>BdF-Mitgliedsnummer: <b>'.$mitglied->memberId.'</b></li>';
		$text .= '<li>ICCF-ID: <b>'.$mitglied->memberInternationalId.'</b></li>';
		$text .= '<li>Vorname: <b>'.$mitglied->vorname.'</b></li>';
		$text .= '<li>Nachname: <b>'.$mitglied->nachname.'</b></li>';
		$text .= '<li>E-Mail: <b>'.$mitglied->email1.'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Spieler</h3>';
		$text .= '<ul>';
		$text .= '<li>Brett 1: <b>'.$data['brett1'].'</b></li>';
		$text .= '<li>Brett 2: <b>'.$data['brett2'].'</b></li>';
		$text .= '<li>Brett 3: <b>'.$data['brett3'].'</b></li>';
		$text .= '<li>Brett 4: <b>'.$data['brett4'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Sonstiges</h3>';
		$text .= '<ul>';
		$text .= '<li>Bemerkungen: <b>'.$data['bemerkungen'].'</b></li>';
		$text .= '</ul>';
		$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';

		// Email an Turnierdirektor verschicken
		$objEmail = new Email();
		$objEmail->charset = 'utf-8';
		$objEmail->from = Config::get('fernschach_emailAdresse');
		$objEmail->fromName = Config::get('fernschach_emailVon');
		$objEmail->subject = 'Mannschaftsmeldung '.$data['vereinsname'];
		$objEmail->html = $text;
		$objEmail->sendTo(array(Config::get('fernschach_turnierdirektorName').' <'.Config::get('fernschach_turnierdirektorEmail').'>'));

		return true;
	}

	/**
	 * Funktion getTournaments
	 * =======================
	 * Turniere einlesen: veröffentlicht, Online-Anmeldung aktiv, ohne Meldedatum oder Meldedatum kleiner akt. Datum
	 *
	 * param $saldo       Float      Saldo des Beitragskontos
	 *
	 * @return array
	 */
	public function getTournaments($mitglied, $saldo)
	{
		$Turniere = array();
		$zeit = time();
		$monat = date('m', $zeit);
		$tag = date('d', $zeit);
		$jahr = date('Y', $zeit);
		$aktuellesDatum = mktime(0, 0, 0, $monat, $tag, $jahr);

		// Meldefähige Turniere laden
		$objTurniere = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE (registrationDate >= ? OR registrationDate = ?) AND onlineAnmeldung = ? AND published = ? AND typ = ? ORDER BY title AND art")
		                                       ->execute($aktuellesDatum, 0, 1, 1, 'm');

		while($objTurniere->next())
		{
			// Turniere überspringen, für die dieser Mannschaftsführer schon
			// gemeldet hat (siehe self::bereitsGemeldet)
			if(self::bereitsGemeldet($objTurniere, $mitglied->id))
			{
				continue;
			}

			$meldedatum = $objTurniere->registrationDate ? ' | Meldedatum: '.date('d.m.Y', $objTurniere->registrationDate) : ' | ohne Meldedatum';
			$nenngeld = ' | Nenngeld: '.trim(str_replace('.', ',', sprintf('%0.2f', $objTurniere->nenngeld))).' €';
			if($mitglied->sepaBeitrag)
			{
				$Turniere[$objTurniere->id] = $objTurniere->title.$nenngeld.$meldedatum;
			}
			else
			{
				if($saldo >= 0)
				{
					$Turniere[$objTurniere->id] = $objTurniere->title.$nenngeld.$meldedatum;
				}
			}
		}

		return $Turniere;
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

		if($intMax < 1 || !$spieler)
		{
			return false;
		}

		$objBuchungen = Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_spieler_konto_nenngeld WHERE pid = ? AND turnier = ? AND typ = ? AND kategorie = ?")
		                                        ->execute($spieler, $objTurnier->id, 's', 's');

		return (int) $objBuchungen->anzahl >= $intMax;
	}

}
