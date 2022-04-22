<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package   Linkscollection
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2016 - 2017
 */
namespace Schachbulle\ContaoFernschachBundle\Modules;

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


		// Template füllen
		$this->Template->daten = self::Formular();
		//if($this->currentCategory) $this->Template->form = $this->SendlinkForm();
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
		
		// URL für action festlegen. Standard ist die Seite auf der das Formular eingebunden ist.
		// $objForm->setFormActionFromUri();
		
		$objForm->addFormField('fieldset1_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Persönliche Daten</legend>'
			)
		)); 
		$objForm->addFormField('memberId', array(
			'label'         => 'Mitgliedsnummer',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false)
		));
		$objForm->addFormField('memberId_info', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<i>oder Spielnummer (falls bekannt)</i>'
			)
		)); 
		$objForm->addFormField('vorname', array(
			'label'         => 'Vorname',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>true)
		));
		$objForm->addFormField('nachname', array(
			'label'         => 'Nachname',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>true)
		));
		$objForm->addFormField('strasse', array(
			'label'         => 'Straße, Nr.',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false)
		));
		$objForm->addFormField('plz', array(
			'label'         => 'PLZ',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false)
		));
		$objForm->addFormField('ort', array(
			'label'         => 'Ort',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false)
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
				'html' => '<fieldset><legend>Turniere</legend><b>Hiermit melde ich mich zu folgenden Fernschachturnieren (bis zu 4 möglich in einem Formular):</b>'
			)
		)); 
		for($x = 1; $x < 5;$x++)
		{
			$objForm->addFormField('turnierbox'.$x.'_start', array(
				'inputType' => 'html',
				'eval' => array
				(
					'html' => '<div style="display:flex;">'
				)
			)); 
			$objForm->addFormField('turnier'.$x, array(
				'label'         => 'Turnier',
				'inputType'     => 'select',
				'options'       => self::getTournaments(),
				'eval'          => array('mandatory'=>false, 'choosen'=>true, 'includeBlankOption' => true)
			));
			$objForm->addFormField('anzahl'.$x, array(
				'label'         => 'Anzahl',
				'inputType'     => 'text',
				'eval'          => array('mandatory'=>false)
			));
			$objForm->addFormField('nenngeld'.$x, array(
				'label'         => 'Nenngeld in EUR',
				'inputType'     => 'text',
				'eval'          => array('mandatory'=>false,'rgxp'=> 'digit')
			));
			$objForm->addFormField('turnierbox'.$x.'_ende', array(
				'inputType' => 'html',
				'eval' => array
				(
					'html' => '</div>'
				)
			)); 
		}
		$objForm->addFormField('fieldset2_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		)); 

		$objForm->addFormField('fieldset3_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Das Nenngeld...</legend>'
			)
		)); 
		$objForm->addFormField('ueberweisung', array(
			'label'         => 'wurde überwiesen am',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false)
		));
		$objForm->addFormField('guthaben', array(
			'label'         => array('', 'soll von meinem Guthaben beim BdF abgebucht werden'),
			'inputType'     => 'checkbox',
			'eval'          => array('mandatory'=>false)
		));
		$objForm->addFormField('fieldset3_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		)); 

		$objForm->addFormField('fieldset4_start', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<fieldset><legend>Weitere Angaben</legend>'
			)
		)); 
		$objForm->addFormField('email', array(
			'label'         => 'E-Mail',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>true, 'rgxp'=>'email')
		));
		$objForm->addFormField('fax', array(
			'label'         => 'Fax',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false, 'rgxp'=>'phone')
		));
		$objForm->addFormField('fieldset4_ende', array(
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
		$objForm->addFormField('bemerkungen_info', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '<strong>(Zahlungswege - siehe <a href="zahlungsverkehr.html" target="_blank" rel="noopener">Hinweise unter "Offizielles"</a>)</strong>'
			)
		)); 
		$objForm->addFormField('fieldset6_ende', array(
			'inputType' => 'html',
			'eval' => array
			(
				'html' => '</fieldset>'
			)
		)); 

		$objForm->addFormField('datenschutz', array(
			'label'         => array('', '<span>Ich willige ein, dass der Deutsche Fernschachbund die von mir übermittelten Informationen und Daten dazu verwendet, um mit mir in Kontakt zu treten, hierüber zu kommunizieren und meine Anfrage abzuwickeln. Dies gilt insbesondere für die Verwendung der E-Mail-Adresse zum vorgenannten Zweck. Die Datenschutzerklärung kann <a href="datenschutz.html" target="_blank">hier</a> eingesehen werden.</span>'),
			'inputType'     => 'checkbox',
			'eval'          => array('mandatory'=>true)
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
			//\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			//\Controller::reload(); 
		}
		
		// Formular als String zurückgeben
		return $objForm->generate();

	}

	protected function saveMeldung($data)
	{
		//print_r($data);
		// Datenbank aktualisieren
		$zeit = time();
		
		// Turniere aus der Meldung der Reihe nach prüfen
		for($x = 1; $x < 5; $x++)
		{
			if($data['turnier'.$x])
			{
				// Spieler in tl_fernschach_spieler suchen
				$objSuche = \Database::getInstance()->prepare('SELECT id FROM tl_fernschach_spieler WHERE nachname=? AND vorname=? AND memberId=?')
				                                    ->limit(1)
				                                    ->execute($data['nachname'], $data['vorname'], $data['memberId']);
				if($objSuche->numRows)
				{
					// Spieler gefunden
					$spielerId = $objSuche->id;
				}
				else
				{
					$set = array
					(
						'tstamp'      => $zeit,
						'nachname'    => $data['nachname'],
						'vorname'     => $data['vorname'],
						'plz'         => $data['plz'],
						'ort'         => $data['ort'],
						'strasse'     => $data['strasse'],
						'email'       => $data['email'],
						'memberId'    => $data['memberId'],
					);
					// Spieler nicht gefunden, dann neu eintragen
					$objInsert = \Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler %s')
					                                     ->set($set)
					                                     ->execute();
					$spielerId = $objInsert->insertId;
				}
				
				// Anzahl Meldungen korrigieren
				$data['anzahl'.$x] = $data['anzahl'.$x] ? (int)$data['anzahl'.$x] : 1;

				for($y = 0; $y < $data['anzahl'.$x]; $y++)
				{
					$set = array
					(
						'tstamp'            => $zeit,
						'spielerId'         => $spielerId,
						'vorname'           => $data['vorname'],
						'nachname'          => $data['nachname'],
						'plz'               => $data['plz'],
						'ort'               => $data['ort'],
						'strasse'           => $data['strasse'],
						'email'             => $data['email'],
						'fax'               => $data['fax'],
						'memberId'          => $data['memberId'],
						'meldungDatum'      => $zeit,
						'meldungTurnier'    => $data['turnier'.$x],
						'meldungAnzahl'     => 1,
						'meldungNenngeld'   => ($y == 0) ? $data['nenngeld'.$x] : '', // Nenngeld nur bei 1. Meldung eintragen
						'nenngeldDate'      => $data['ueberweisung'],
						'nenngeldGuthaben'  => $data['guthaben'],
						'infoQualifikation' => $data['qualifikation'],
						'bemerkungen'       => $data['bemerkungen'],
						'published'         => 1
					);
					$objInsert = \Database::getInstance()->prepare('INSERT INTO tl_fernschach_meldungen %s')
					                                     ->set($set)
					                                     ->execute();
				}
			}
		}

        //
		//\System::log('[Linkscollection] New Link submitted: '.$data['title'].' ('.$data['url'].')', __CLASS__.'::'.__FUNCTION__, TL_CRON);
        //
		//// Email an Admin verschicken
		//$objEmail = new \Email();
		//$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		//$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		//$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['linkscollection_subject'], \Idna::decode(\Environment::get('host')));
        //
		//// Kommentar zusammenbauen
		//$strComment .= 'Titel: '.$data['title']."\n";
		//$strComment .= 'URL: '.$data['url']."\n";
		//$strComment .= 'Kategorie: '.$data['category']."\n";
		//$strComment .= 'Beschreibung: '.$data['description'];
        //
		//// Add the comment details
		//$objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['linkscollection_message'],
		//                          $data['name'] . ' (' . $data['email'] . ')',
		//                          $strComment,
		//                          \Idna::decode(\Environment::get('base')) . \Environment::get('request'),
		//                          \Idna::decode(\Environment::get('base')) . 'contao/main.php?do=linkscollection&table=tl_linkscollection_links&act=edit&id=' . $objLink->insertId);
        //
		//$objEmail->sendTo(array($GLOBALS['TL_ADMIN_NAME'].' <'.$GLOBALS['TL_ADMIN_EMAIL'].'>'));
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
		$objForms = $this->Database->prepare("SELECT * FROM tl_fernschach_turniere WHERE (registrationDate > ? OR registrationDate = ?) AND onlineAnmeldung = ? AND published = ? ORDER BY titel")
		                           ->execute(time(), 0, 1, 1);

		while ($objForms->next())
		{
			$meldedatum = $objForms->registrationDate ? ' (Meldedatum: '.date('d.m.Y', $objForms->registrationDate).')' : ' (ohne Meldedatum)';
			$arrForms[$objForms->id] = $objForms->titel.$meldedatum;
		}

		return $arrForms;
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

}
