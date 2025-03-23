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

use Codefog\HasteBundle\Form\Form;

class Meldeformular extends \Module
{

	protected $strTemplate = 'mod_fernschach';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH MELDEFORMULAR ###';
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
			$this->import('FrontendUser','User'); // Frontend-Mitglied laden
			if($this->User)
			{
				if(!$this->User->isMemberOf($GLOBALS['TL_CONFIG']['fernschach_memberFernschach']))
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

		// Der 1. Parameter ist die Formular-ID (hier "linkform")
		// Der 2. Parameter ist GET oder POST
		// Der 3. Parameter ist eine Funktion, die entscheidet wann das Formular gesendet wird (Third is a callable that decides when your form is submitted)
		// Der optionale 4. Parameter legt fest, ob das ausgegebene Formular auf Tabellen basiert (true)
		// oder nicht (false) (You can pass an optional fourth parameter (true by default) to turn the form into a table based one)
		$objForm = new \Haste\Form\Form('meldeform', 'POST', function($objHaste)
		{
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});

		// BdF-Mitgliedsdaten laden
		$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpielerdatensatz(\FrontendUser::getInstance()->fernschach_memberId);
		$salden_haupt = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo(\FrontendUser::getInstance()->fernschach_memberId, '');
		$salden_beitrag = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo(\FrontendUser::getInstance()->fernschach_memberId, 'beitrag');
		$salden_nenngeld = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo(\FrontendUser::getInstance()->fernschach_memberId, 'nenngeld');
		$mitgliedsdaten = '<h4>Angemeldeter Benutzer</h4>';
		$mitgliedsdaten .= '<ul>';
		$mitgliedsdaten .= '<li>Anmeldename: <b>'.\FrontendUser::getInstance()->username.'</b></li>';
		$mitgliedsdaten .= '<li>Vor- und Nachname: <b>'.\FrontendUser::getInstance()->firstname.' '.\FrontendUser::getInstance()->lastname.'</b></li>';
		$mitgliedsdaten .= '<li>E-Mail-Adresse: <b>'.\FrontendUser::getInstance()->email.'</b></li>';
		$mitgliedsdaten .= '</ul>';
		$mitgliedsdaten .= '<h4>Zugeordnetes BdF-Mitglied</h4>';
		$mitgliedsdaten .= '<ul>';
		$mitgliedsdaten .= '<li>Vor- und Nachname: <b>'.$mitglied->vorname.' '.$mitglied->nachname.'</b></li>';
		$mitgliedsdaten .= '<li>Mitgliedsnummer: <b>'.$mitglied->memberId.'</b></li>';
		$mitgliedsdaten .= '<li>E-Mail-Adresse 1: <b>'.$mitglied->email1.'</b></li>';
		$mitgliedsdaten .= '<li>E-Mail-Adresse 2: <b>'.$mitglied->email2.'</b></li>';
		// Saldo Hauptkonto ermitteln und ausgeben
		$value = end($salden_haupt);
		if($value >= 0)
		{
			$html_start = '<span style="color:green;">';
			$html_ende = ' €<span>';
		}
		else
		{
			$html_start = '<span style="color:red;">';
			$html_ende = ' €<span>';
		}
		$saldo = str_replace('.', ',', sprintf('%0.2f',$value));
		$mitgliedsdaten .= '<li>Kontostand Hauptkonto: <b>'.$html_start.$saldo.$html_ende.'</b></li>';
		// Saldo Beitragskonto ermitteln und ausgeben
		$value = end($salden_beitrag);
		if($value >= 0)
		{
			$html_start = '<span style="color:green;">';
			$html_ende = ' €<span>';
		}
		else
		{
			$html_start = '<span style="color:red;">';
			$html_ende = ' €<span>';
		}
		$saldo = str_replace('.', ',', sprintf('%0.2f',$value));
		$mitgliedsdaten .= '<li>Kontostand Beitrag: <b>'.$html_start.$saldo.$html_ende.'</b></li>';
		// Saldo Nenngeldkonto ermitteln und ausgeben
		$value = end($salden_nenngeld);
		if($value >= 0)
		{
			$html_start = '<span style="color:green;">';
			$html_ende = ' €<span>';
		}
		else
		{
			$html_start = '<span style="color:red;">';
			$html_ende = ' €<span>';
		}
		$saldo = str_replace('.', ',', sprintf('%0.2f',$value));
		$mitgliedsdaten .= '<li>Kontostand Nenngeld: <b>'.$html_start.$saldo.$html_ende.'</b></li>';

		$mitgliedsdaten .= '</ul>';

		$objForm->addFormField('fieldset1_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Persönliche Daten</legend>'
			)
		));
		$objForm->addFormField('mitgliedsdaten', array(
			'inputType'     => 'html',
			'eval' => array
			(
				'html' => $mitgliedsdaten
			)
		));
		$objForm->addFormField('fieldset1_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		));

		$objForm->addFormField('fieldset2_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Turnier</legend><b>Hiermit melde ich mich zu folgendem Fernschachturnier an:</b>'
			)
		));
		$objForm->addFormField('turnierbox_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<div style="display:flex;">'
			)
		));
		$objForm->addFormField('turnier', array(
			'label'         => 'Turnier',
			'inputType'     => 'select',
			'options'       => self::getTournaments(),
			'eval'          => array('mandatory'=>false, 'choosen'=>true, 'includeBlankOption' => true)
		));
		$objForm->addFormField('turnierbox_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</div>'
			)
		));
		$objForm->addFormField('fieldset2_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		));

		$objForm->addFormField('fieldset5_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Bei Aufstiegsturnieren: Letzte Qualifikation für die H- oder M-Klasse</legend>'
			)
		));
		$objForm->addFormField('qualifikation', array(
			'label'         => 'Turnierkennzeichen und Punktestand',
			'inputType'     => 'textarea',
			'eval'          => array('mandatory'=>false, 'rte'=>'tinyMCE')
		));
		$objForm->addFormField('fieldset5_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		));

		$objForm->addFormField('fieldset6_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Bemerkungen</legend>'
			)
		));
		$objForm->addFormField('bemerkungen', array(
			'label'         => 'Sonstiges (z.B. Urlaub von ... bis ...)',
			'inputType'     => 'textarea',
			'eval'          => array('mandatory'=>false, 'rte'=>'tinyMCE')
		));
		$objForm->addFormField('fieldset6_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		));

		// Submit-Button hinzufügen
		$objForm->addFormField('submit', array(
			'label'         => 'Absenden',
			'inputType'     => 'submit',
			'eval'          => array('class'=>'btn btn-primary')
		));
		$objForm->addCaptchaFormField('captcha');
		// Ausgeblendete Felder FORM_SUBMIT und REQUEST_TOKEN automatisch hinzufügen.
		// Nicht verwenden wenn generate() anschließend verwendet, da diese Felder dort standardmäßig bereitgestellt werden.
		// $objForm->addContaoHiddenFields();

		// validate() prüft auch, ob das Formular gesendet wurde
		if($objForm->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $objForm->fetchAll();
			self::saveMeldung($arrData); // Daten sichern
			// Seite neu laden
			\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			\Controller::reload();
		}

		// Formular als String zurückgeben
		return $objForm->generate();

	}

	protected function saveMeldung($data)
	{
		//print_r($data);
		// Datenbank aktualisieren

		$zeit = time();

		// Mitgliedsdaten laden
		$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpielerdatensatz(\FrontendUser::getInstance()->fernschach_memberId);

		// Turnier prüfen
		if($data['turnier'])
		{
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
			//print_r($set);
			$objInsert = \Database::getInstance()->prepare('INSERT INTO tl_fernschach_turniere_meldungen %s')
			                                     ->set($set)
			                                     ->execute();
			$meldungId = $objInsert->insertId;

			// Turnier laden
			$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($data['turnier']);

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
				'verwendungszweck'  => 'Nenngeld-Forderung',
				'turnier'           => $data['turnier'],
				'comment'           => 'Datensatz erzeugt durch Turnieranmeldung am '.date('d.m.Y H:i', $zeit),
				'meldungId'         => $meldungId,
				'published'         => '1'
			);
			$objInsert = \Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler_konto_nenngeld %s')
			                                     ->set($set)
			                                     ->execute();
		}

        //
		//\System::log('[Linkscollection] New Link submitted: '.$data['title'].' ('.$data['url'].')', __CLASS__.'::'.__FUNCTION__, TL_CRON);
        //
		$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($data['turnier']);

		// E-Mail für Turnierleiter zusammenbauen
		$turnierleiter = self::getTurnierleiter($data['turnier']);

		if(isset($turnierleiter[0]))
		{
			// Email verschicken
			$objEmail = new \Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
			$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
			$objEmail->subject = 'Turnieranmeldung '.$objTurnier->title;
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
			// Kommentar zusammenbauen
			$text = '<html><head><title></title></head><body>';
			$text .= '<p>Eine neue Turnieranmeldung wurde vorgenommen:</p>';
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
			$objEmail = new \Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
			$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
			$objEmail->subject = 'Turnieranmeldung '.$objTurnier->title;
			// Kommentar zusammenbauen
			$text = '<html><head><title></title></head><body>';
			$text .= '<p>Sie haben eine Turnieranmeldung vorgenommen:</p>';
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
	}

	/**
	 * Funktion getTournaments
	 * =======================
	 * Turniere einlesen: veröffentlicht, Online-Anmeldung aktiv, ohne Meldedatum oder Meldedatum kleiner akt. Datum
	 *
	 * @return array
	 */
	public function getTournaments()
	{
		$arrForms = array();
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE (registrationDate > ? OR registrationDate = ?) AND onlineAnmeldung = ? AND published = ? ORDER BY art AND title")
		                                       ->execute(time(), 0, 1, 1);

		while($objTurniere->next())
		{
			$published = self::TurnierkategorieVeroeffentlicht($objTurniere->pid); // Prüfen, ob alle übergeordneten Turnierkategorien veröffentlicht sind

			if($published)
			{
				$meldedatum = $objTurniere->registrationDate ? '  | Meldedatum: '.date('d.m.Y', $objForms->registrationDate) : '  | ohne Meldedatum';
				$nenngeld = ' | Nenngeld: '.str_replace('.', ',', sprintf('%0.2f', $objTurniere->nenngeld)).' € ';
				$arrForms[$objTurniere->id] = $objTurniere->title.$nenngeld.$meldedatum;
			}
		}

		return $arrForms;
	}

	/*
	 * Funktion TurnierkategorieVeroeffentlicht
	 * Liefert true/false, je nach veröffentlichten Oberkategorien
	 */
	private function TurnierkategorieVeroeffentlicht($id)
	{
		while($id > 0)
		{
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
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
	 * Funktion getTurnierleiter
	 * Liefert die Turnierleiter (Name und E-Mail) eines Turniers und seiner Oberkategorien
	 */
	private function getTurnierleiter($id)
	{
		$arr = array();
		while($id > 0)
		{
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);

			if($objTurnier->published && $objTurnier->turnierleiterInfo && $objTurnier->turnierleiterEmail)
			{
				// Turnierleiter speichern
				$arr[] = array
				(
					'name'    => $objTurnier->turnierleiterName,
					'email'   => $objTurnier->turnierleiterEmail
				);
			}
			$id = $objTurnier->pid; // Neue ID setzen
		}
		return $arr;
	}

	/*
	 * Confirmation
	 */
	private function sendNotification($arrData, $objMember)
	{
		$arrTokens['member_id'] = $objMember->id;
		$arrTokens['member_email'] = $objMember->email;
		$arrTokens['member_firstname'] = $objMember->firstname;
		$arrTokens['member_lastname'] = $objMember->lastname;

		foreach ($arrData as $key => $data) {
			$arrTokens['form_' . $key] = $data;
		}

		$calendar = CalendarModel::findOneById($arrData['calendar_id']);
		$arrTokens['form_calendar_title'] = $calendar->title;

		$objNotification = Notification::findByPk($this->nc_notification);
		if (null !== $objNotification) {
			$objNotification->send($arrTokens);
		}
	}

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
