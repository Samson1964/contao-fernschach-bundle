<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   DeWIS
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

namespace Schachbulle\ContaoFernschachBundle\Modules;

class Titel extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_titeltraeger';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH-VERWALTUNG - LISTE TITELTRÄGER ###';
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

		// Gewünschten Titel laden
		$objTitel = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler_titel WHERE titel = ? ORDER BY datum DESC')
		                                    ->execute($this->fernschachverwaltung_titel);

		$daten = array();
		
		$titel = \Schachbulle\ContaoFernschachBundle\Classes\Titel::get(); // Titel der veröffentlichten Spieler laden
		
		// Titel und Normen auslesen
		if($objTitel->numRows)
		{
			while($objTitel->next())
			{
				// Gewünschten Titel laden
				$objSpieler = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler WHERE id = ?')
				                                      ->execute($objTitel->pid);
				$daten[] = array
				(
					'bdfid'      => $objSpieler->memberId,
					'iccfid'     => $objSpieler->memberInternationalId,
					'name'       => $objSpieler->nachname.','.$objSpieler->vorname,
					'ort'        => $objSpieler->ort,
					'verstorben' => $objSpieler->death,
					'datum'      => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objTitel->datum),
					'jahr'       => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate(substr($objTitel->datum, 0, 4)),
				);
			}
		}
		// Ausgabe
		$this->Template->daten = $daten;

	}

}
