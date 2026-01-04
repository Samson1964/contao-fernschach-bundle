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

class Meldeformular_Mannschaft extends \Module
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

		// BdF-Mitgliedsdaten laden
		$records = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE published = ? AND archived = ? ORDER BY nachname ASC, vorname ASC")
		                                   ->execute('1', '');
		$mitglieder = array();
		if($records->numRows)
		{
			while($records->next())
			{
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($records);
				if($mitglied)
				{
					// Nur Mitglieder berücksichtigen
					$saldo_beitrag = end(\Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($records->id, 'beitrag'));
					if($saldo_beitrag >= 0)
					{
						// Nur Nichtrückstand bei Beitrag berücksichtigen
						$datensatz = $records->nachname.','.$records->vorname.' (BdF-Nr. '.$records->memberId.')';
						$mitglieder[$datensatz] = $datensatz;
					}
				}
			}
		}

		$form = new \Schachbulle\ContaoHelperBundle\Classes\Form();
		$form->addField(array('typ' => 'hidden', 'name' => 'FORM_SUBMIT', 'value' => 'form_turnieranmeldung'));
		$form->addField(array('typ' => 'hidden', 'name' => 'REQUEST_TOKEN', 'value' => REQUEST_TOKEN));
		$form->addField(array('typ' => 'hidden', 'name' => 'registrierung', 'value' => time()));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Angaben zur Mannschaft'));
		$form->addField(array('typ' => 'text', 'name' => 'vereinsname', 'label' => 'Genauer Name des Vereins bzw. der Spielgemeinschaft', 'mandatory' => true));
		$form->addField(array('typ' => 'text', 'name' => 'vereinsname_alt', 'label' => 'Alter Name der Mannschaft (bei Namensänderung)'));
		$form->addField(array('typ' => 'text', 'name' => 'mannschaftsname', 'label' => 'Genaue Bezeichnung der gemeldeten Mannschaft (ggf. mit römischer Ziffer bei mehreren Teams)', 'mandatory' => true));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Mannschaftsführer'));
		$form->addField(array('typ' => 'select', 'name' => 'mannschaftsleiter', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Spieler'));
		$form->addField(array('typ' => 'select', 'name' => 'brett1', 'label' => 'Brett 1', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'select', 'name' => 'brett2', 'label' => 'Brett 2', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'select', 'name' => 'brett3', 'label' => 'Brett 3', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'select', 'name' => 'brett4', 'label' => 'Brett 4', 'mandatory' => true, 'options' => $mitglieder));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'fieldset', 'label' => 'Angaben zum Melder'));
		$form->addField(array('typ' => 'text', 'name' => 'melder_vorname', 'label' => 'Ihr Vorname', 'mandatory' => true));
		$form->addField(array('typ' => 'text', 'name' => 'melder_nachname', 'label' => 'Ihr Nachname', 'mandatory' => true));
		$form->addField(array('typ' => 'text', 'name' => 'melder_email', 'label' => 'Ihre E-Mail-Adresse', 'mandatory' => true));
		$form->addField(array('typ' => 'fieldset', 'label' => ''));
		$form->addField(array('typ' => 'textarea', 'name' => 'bemerkungen', 'label' => 'Bemerkungen'));
		$form->addField(array('typ' => 'submit', 'label' => 'Anmeldung absenden'));
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

		// Text für Absender zusammenbauen
		$text = '<html><head><title></title></head><body>';
		$text .= '<h3>Angaben zur Mannschaft</h3>';
		$text .= '<ul>';
		$text .= '<li>Vereinsname: <b>'.$data['vereinsname'].'</b></li>';
		$text .= '<li>Alter Vereinsname: <b>'.$data['vereinsname_alt'].'</b></li>';
		$text .= '<li>Mannschaftsname: <b>'.$data['mannschaftsname'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Mannschaftsführer</h3>';
		$text .= '<ul>';
		$text .= '<li><b>'.$data['mannschaftsleiter'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Spieler</h3>';
		$text .= '<ul>';
		$text .= '<li>Brett 1: <b>'.$data['brett1'].'</b></li>';
		$text .= '<li>Brett 2: <b>'.$data['brett2'].'</b></li>';
		$text .= '<li>Brett 3: <b>'.$data['brett3'].'</b></li>';
		$text .= '<li>Brett 4: <b>'.$data['brett4'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Angaben zum Melder</h3>';
		$text .= '<ul>';
		$text .= '<li>Vorname: <b>'.$data['melder_vorname'].'</b></li>';
		$text .= '<li>Nachname: <b>'.$data['melder_nachname'].'</b></li>';
		$text .= '<li>E-Mail: <b>'.$data['melder_email'].'</b></li>';
		$text .= '<li>Bemerkungen: <b>'.$data['bemerkungen'].'</b></li>';
		$text .= '</ul>';
		$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';

		// Email an Absender verschicken
		$objEmail = new \Email();
		$objEmail->charset = 'utf-8';
		$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
		$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
		$objEmail->subject = 'Mannschaftsmeldung '.$data['vereinsname'];
		$objEmail->html = $text;
		$objEmail->sendTo(array($data['melder_vorname'].' '.$data['melder_nachname'].' <'.$data['melder_email'].'>'));

		// Text für Turnierdirektor zusammenbauen
		$text = '<html><head><title></title></head><body>';
		$text .= '<h3>Angaben zur Mannschaft</h3>';
		$text .= '<ul>';
		$text .= '<li>Vereinsname: <b>'.$data['vereinsname'].'</b></li>';
		$text .= '<li>Alter Vereinsname: <b>'.$data['vereinsname_alt'].'</b></li>';
		$text .= '<li>Mannschaftsname: <b>'.$data['mannschaftsname'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Mannschaftsführer</h3>';
		$text .= '<ul>';
		$text .= '<li><b>'.$data['mannschaftsleiter'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Spieler</h3>';
		$text .= '<ul>';
		$text .= '<li>Brett 1: <b>'.$data['brett1'].'</b></li>';
		$text .= '<li>Brett 2: <b>'.$data['brett2'].'</b></li>';
		$text .= '<li>Brett 3: <b>'.$data['brett3'].'</b></li>';
		$text .= '<li>Brett 4: <b>'.$data['brett4'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Angaben zum Melder</h3>';
		$text .= '<ul>';
		$text .= '<li>Vorname: <b>'.$data['melder_vorname'].'</b></li>';
		$text .= '<li>Nachname: <b>'.$data['melder_nachname'].'</b></li>';
		$text .= '<li>E-Mail: <b>'.$data['melder_email'].'</b></li>';
		$text .= '<li>Bemerkungen: <b>'.$data['bemerkungen'].'</b></li>';
		$text .= '</ul>';
		$text .= '<h3>Hinweise für den Turnierdirektor</h3>';
		$text .= '<ul>';
		$text .= '<li>Das Nenngeld muß dem Mannschaftsführer in Rechnung gestellt werden. Dazu ist in Contao ein Datensatz im Nenngeld beim Mannschaftsführer zu erzeugen.</li>';
		$text .= '<li>Eine automatische Erzeugung der Nenngeld-Sollbuchung ist nicht möglich.</li>';
		$text .= '</ul>';
		$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';

		// Email an Turnierdirektor verschicken
		$objEmail = new \Email();
		$objEmail->charset = 'utf-8';
		$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
		$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
		$objEmail->subject = 'Mannschaftsmeldung '.$data['vereinsname'];
		$objEmail->html = $text;
		$objEmail->sendTo(array($GLOBALS['TL_CONFIG']['fernschach_turnierdirektorName'].' <'.$GLOBALS['TL_CONFIG']['fernschach_turnierdirektorEmail'].'>'));
	}
}
