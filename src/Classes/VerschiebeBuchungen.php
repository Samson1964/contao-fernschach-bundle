<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class VerschibeBuchungen
  */
class VerschiebeBuchungen extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Importiert eine Buchungsliste
	 */
	public function run()
	{

		if(\Input::get('key') != 'verschiebeBuchungen')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');

		$verwendungszweck = array();
		// Verwendungszwecke finden und sortieren nach Anzahl Vorkommen
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				if(isset($verwendungszweck[$objBuchungen->verwendungszweck]))
				{
					$verwendungszweck[$objBuchungen->verwendungszweck]++;
				}
				else
				{
					$verwendungszweck[$objBuchungen->verwendungszweck] = 1;
				}
			}
			arsort($verwendungszweck); // Array nach Werten absteigend sortieren
		}
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_beitrag")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				if(isset($verwendungszweck[$objBuchungen->verwendungszweck]))
				{
					$verwendungszweck[$objBuchungen->verwendungszweck]++;
				}
				else
				{
					$verwendungszweck[$objBuchungen->verwendungszweck] = 1;
				}
			}
			arsort($verwendungszweck); // Array nach Werten absteigend sortieren
		}
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				if(isset($verwendungszweck[$objBuchungen->verwendungszweck]))
				{
					$verwendungszweck[$objBuchungen->verwendungszweck]++;
				}
				else
				{
					$verwendungszweck[$objBuchungen->verwendungszweck] = 1;
				}
			}
			arsort($verwendungszweck); // Array nach Werten absteigend sortieren
		}

		// Verwendungszweck modifizieren: array('Titel' => 'Anzahl') wird array('Titel' => 'Titel (Anzahl mal)')
		foreach($verwendungszweck as $key => $value)
		{
			$verwendungszweck[$key] = $key.' ('.$value.' mal)';
		}
		
		$form = new \Schachbulle\ContaoFernschachBundle\Classes\DCAParser('tl_dca');
		$form->setBacklink(ampersand(str_replace('&key=verschiebeBuchungen', '', \Environment::get('request'))));   
		$dca = array
		(
			'submit' => 'Verschiebung starten',
			'info' => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['hinweis'],
			'fieldsets' => array
			(
				'title_legend' => array
				(
					'title' => 'Buchungsdatum und Verwendungszweck',
					'fields' => array
					(
						'datum' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['datum'],
							'inputType'  => 'text',
							'eval'       => array('tl_class' => 'w50 widget wizard', 'datepicker'=>true, 'rgxp'=>'date', 'mandatory'=>false)
						),
						'search_verwendungszweck' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['search_verwendungszweck'],
							'inputType'  => 'text',
							'eval'       => array('tl_class' => 'w50 clr widget')
						),
						'select_verwendungszweck' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['select_verwendungszweck'],
							'inputType'  => 'select',
							'options'    => $verwendungszweck,
							'eval'       => array('tl_class' => 'w50 widget', 'chosen'=>true, 'includeBlankOption'=>true)
						)
					)
				),
				'konto_legend' => array
				(
					'title' => 'Zielkonto',
					'fields' => array
					(
						'zielkonto' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['zielkonto'],
							'inputType'  => 'select',
							'options'    => array('h'=>'Hauptkonto','b'=>'Beitragskonto','n'=>'Nenngeldkonto'),
							'eval'       => array('tl_class' => 'w50 widget', 'chosen'=>true, 'includeBlankOption'=>true)
						),
					),
				),
			)
		);
		$form->setDCA($dca);
		// Formular wurde abgeschickt und ist korrekt
		//if($form->isSubmitted() && $form->validate())
		if($form->isSubmitted())
		{
			$daten = $form->getData();
			self::getImport($daten); // Daten sichern
			// Seite neu laden
			// Cookie setzen und zurückkehren zur Buchungsliste
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=verschiebeBuchungen', '', \Environment::get('request')));
		}
		return $form->parse();

	}

	public function getImport($daten)
	{
		
		// Verwendungszwecke finden und sortieren nach Anzahl Vorkommen
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto")
		                                        ->execute();
	}
}
