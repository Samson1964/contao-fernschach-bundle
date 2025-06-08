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
		if($value != 0) $mitgliedsdaten .= '<li>Kontostand Hauptkonto: <b>'.$html_start.$saldo.$html_ende.'</b></li>';
		// Saldo Beitragskonto ermitteln und ausgeben
		$beitragssaldo = end($salden_beitrag);
		if($beitragssaldo >= 0)
		{
			$html_start = '<span style="color:green;">';
			$html_ende = ' €<span>';
		}
		else
		{
			$html_start = '<span style="color:red;">';
			$html_ende = ' €<span>';
		}
		$saldo = str_replace('.', ',', sprintf('%0.2f',$beitragssaldo));
		$mitgliedsdaten .= '<li>Kontostand Beitrag: <b>'.$html_start.$saldo.$html_ende.'</b></li>';
		// Saldo Nenngeldkonto ermitteln und ausgeben
		$nenngeldsaldo = end($salden_nenngeld);
		if($nenngeldsaldo >= 0)
		{
			$html_start = '<span style="color:green;">';
			$html_ende = ' €<span>';
		}
		else
		{
			$html_start = '<span style="color:red;">';
			$html_ende = ' €<span>';
		}
		$saldo = str_replace('.', ',', sprintf('%0.2f',$nenngeldsaldo));
		$mitgliedsdaten .= '<li>Kontostand Nenngeld: <b>'.$html_start.$saldo.$html_ende.'</b></li>';

		// SEPA-Mandate prüfen
		$sepamandate = '';
		$sepacount = 0;
		if($mitglied->sepaBeitrag)
		{
			$sepacount++;
			$sepamandate .= '<img src="bundles/contaofernschach/images/ja.png" width="12"> Beitrag | ';
		}
		else
		{
			$sepamandate .= '<img src="bundles/contaofernschach/images/nein.png" width="12"> Beitrag | ';
		}
		if($mitglied->sepaNenngeld)
		{
			$sepacount++;
			$sepamandate .= '<img src="bundles/contaofernschach/images/ja.png" width="12"> Nenngeld';
		}
		else
		{
			$sepamandate .= '<img src="bundles/contaofernschach/images/nein.png" width="12"> Nenngeld';
		}
		$mitgliedsdaten .= '<li>SEPA-Mandate: <b>'.$sepamandate.'</b></li>';
		if($sepacount != 2)
		{
			//$mitgliedsdaten .= '<li><span style="color:red;">Es fehlen SEPA-Mandate, weshalb die Turnierauswahl nicht möglich oder eingeschränkt sein könnte.</span></li>';
		}
		if(!$mitglied->sepaBeitrag && $beitragssaldo < 0)
		{
			$mitgliedsdaten .= '<li><span style="color:red;">Eine Turnieranmeldung ist wegen fehlendem SEPA-Beitragsmandat bzw. negativem Beitragskonto nicht möglich.</span></li>';
		}
		else
		{
			if(!$mitglied->sepaNenngeld && $nenngeldsaldo < 0)
			{
				$mitgliedsdaten .= '<li><span style="color:red;">Es werden u.U. nicht alle Turniere angezeigt, weil Ihr Nenngeldkonto zu wenig Guthaben hat oder Sie kein SEPA-Mandat für Nenngeld erteilt haben.</span></li>';
			}
		}
		$mitgliedsdaten .= '</ul>';

		// Meldungen des Spielers laden
		$mitgliedsdaten .= '<h4>Letzte 5 Anmeldungen</h4>';
		$mitgliedsdaten .= '<ul>';
		$anmeldungen_bewerbungen = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getAnmeldungenBewerbungen($mitglied->id);
		$nummer = 0;
		foreach($anmeldungen_bewerbungen as $item)
		{
			if($item['typ'] == 'Anmeldung')
			{
				$nummer++;
				$mitgliedsdaten .= '<li>';
				$mitgliedsdaten .= date('d.m.Y H:i', $item['datum']).' '.$item['turnier'];
				$mitgliedsdaten .= '</li>';
			}
			if($nummer == 5) break;
		}
		$mitgliedsdaten .= '</ul>';

		$form = new \Schachbulle\ContaoHelperBundle\Classes\Form();
		$form->addField(array('typ' => 'hidden', 'name' => 'FORM_SUBMIT', 'value' => 'form_turnieranmeldung'));
		$form->addField(array('typ' => 'hidden', 'name' => 'REQUEST_TOKEN', 'value' => REQUEST_TOKEN));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Persönliche Daten'));
		$form->addField(array('typ' => 'explanation', 'label' => $mitgliedsdaten));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		if($mitglied->sepaBeitrag || $beitragssaldo >= 0)
		{
			$form->addField(array('typ' => 'fieldset', 'label' => 'Turnier'));
			$form->addField(array('typ' => 'explanation', 'label' => '<b>Hiermit melde ich mich zu folgendem Fernschachturnier an:</b>'));
			$form->addField(array('typ' => 'select', 'name' => 'turnier', 'mandatory' => true, 'options' => self::getTournaments($mitglied->sepaNenngeld, $nenngeldsaldo, $mitglied->klassenberechtigung)));
			$form->addField(array('typ' => 'fieldset', 'label' => ''));
			$form->addField(array('typ' => 'fieldset', 'label' => 'Bei Aufstiegsturnieren: Letzte Qualifikation für die H- oder M-Klasse'));
			$form->addField(array('typ' => 'textarea', 'name' => 'qualifikation', 'label' => 'Turnierkennzeichen und Punktestand'));
			$form->addField(array('typ' => 'fieldset', 'label' => ''));
			$form->addField(array('typ' => 'fieldset', 'label' => 'Bemerkungen'));
			$form->addField(array('typ' => 'textarea', 'name' => 'bemerkungen', 'label' => 'Sonstiges (z.B. Urlaub von ... bis ...)'));
			$form->addField(array('typ' => 'fieldset', 'label' => ''));
			$form->addField(array('typ' => 'submit', 'label' => 'Anmeldung absenden'));
		}
		// validate() prüft auch, ob das Formular gesendet wurde
		if($form->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $form->fetchAll();
			self::saveMeldung($arrData); // Daten sichern
			// Seite neu laden
			\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			\Controller::reload();
		}

		// Formular als String zurückgeben
		return $form->generate();

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
				'verwendungszweck'  => 'Nenngeld-Forderung '.$objTurnier->title,
				'turnier'           => $data['turnier'],
				'comment'           => 'Datensatz erzeugt durch Turnieranmeldung am '.date('d.m.Y H:i', $zeit),
				'meldungId'         => $meldungId,
				'published'         => '1'
			);
			$objInsert = \Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler_konto_nenngeld %s')
			                                     ->set($set)
			                                     ->execute();
		}

		$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($data['turnier']);

		// E-Mail für Turnierleiter zusammenbauen
		$turnierleiter = \Schachbulle\ContaoFernschachBundle\Classes\Turnier::getTurnierleiter($data['turnier']);

		if(isset($turnierleiter[0]))
		{
			// Email verschicken
			$objEmail = new \Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
			$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
			$objEmail->sendBcc($GLOBALS['TL_CONFIG']['fernschach_emailVon'].' <'.$GLOBALS['TL_CONFIG']['fernschach_emailAdresse'].'>');
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
			// Backend-Link zum Turnier generieren
			$backendlink = $this->replaceInsertTags('{{env::url}}').'/contao?do=fernschach-turniere&table=tl_fernschach_turniere_meldungen&rt='.REQUEST_TOKEN.'&id='.$objTurnier->id;
			// Kommentar zusammenbauen
			$text = '<html><head><title></title></head><body>';
			$text .= '<p>Eine neue Turnieranmeldung wurde vorgenommen:</p>';
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
	public function getTournaments($sepa, $saldo, $klasse)
	{
		$Turniere = array();
		$Standardgruppe = 'Weitere Turniere'; // Name des optgroup-Labels für nichtzugeordnete Turniere

		// Meldefähige Turniere laden
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE (registrationDate > ? OR registrationDate = ?) AND onlineAnmeldung = ? AND published = ? ORDER BY title AND art")
		                                       ->execute(time(), 0, 1, 1);

		while($objTurniere->next())
		{
			$published = self::TurnierkategorieVeroeffentlicht($objTurniere->pid); // Prüfen, ob alle übergeordneten Turnierkategorien veröffentlicht sind
			$Gruppenname = self::Turniergruppe($objTurniere->pid); // Titel der Turnierkategorie laden

			if($published)
			{
				$turnieranmeldung = true;
				// Spielermaximum prüfen
				if($objTurniere->spielerMax > 0)
				{
					// Ein Spielermaximum ist gesetzt, jetzt prüfen ob noch Anmeldungen möglich sind
					$objMeldungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE pid = ? AND published = ?")
					                                        ->execute($objTurniere->id, 1);
					if($objMeldungen->numRows >= $objTurniere->spielerMax)
					{
						$turnieranmeldung = false; // Anmeldung für dieses Turnier überspringen, da maximale Spielerzahl erreicht ist
					}
				}

				// Klasse des Spielers prüfen
				if($klasse == '')
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
					if($objTurniere->klassenzuordnung != '' && $objTurniere->klassenzuordnung != $klasse)
					{
						$turnieranmeldung = false; // Anmeldung für dieses Turnier überspringen, da Klassenberechtigung nicht übereinstimmt
					}
				}

				// Anmeldung in Select-Box eintragen, wenn erlaubt
				if($turnieranmeldung)
				{
					// Optgroup-Label festlegen
					$Gruppe = $Gruppenname ? $Gruppenname : $Standardgruppe;
					if(!isset($Turniere[$Gruppe])) $Turniere[$Gruppe] = array(); // Unterarray anlegen

					$meldedatum = $objTurniere->registrationDate ? ' | Meldedatum: '.date('d.m.Y', $objTurniere->registrationDate) : ' | ohne Meldedatum';
					$nenngeld = ' | Nenngeld: '.trim(str_replace('.', ',', sprintf('%0.2f', $objTurniere->nenngeld))).' €';
					// Turnier eintragen in Liste, wenn vorhandenes Nenngeld ausreicht
					if($sepa || $saldo >= (int)$objTurniere->nenngeld)
					{
						$Turniere[$Gruppe][$objTurniere->id] = $objTurniere->title.$nenngeld.$meldedatum;
					}
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
	 * Funktion TurnierkategorieVeroeffentlicht
	 * Liefert true/false, je nach veröffentlichten Oberkategorien
	 */
	private function Turniergruppe($id)
	{
		$gruppe = '';

		while($id > 0)
		{
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);

			// Gruppenname ermitteln
			if($objTurnier->titleView)
			{
				$gruppe = $objTurnier->titleAlternate ? $objTurnier->titleAlternate : $objTurnier->title;
			}
			$id = $objTurnier->pid; // Neue ID setzen
		}
		return $gruppe;
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
