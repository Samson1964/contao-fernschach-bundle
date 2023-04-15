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

class Kontoauszug extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_kontoauszug';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH-VERWALTUNG - KONTOAUSZUG ###';
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

		// Objekt FrontendUser importieren
		$this->import('FrontendUser','User');

		// FE-Mitglied in Fernschach-Verwaltung suchen, wenn ein FE-Mitglied angemeldet ist
		if($this->User->id)
		{
			if($this->User->fernschach_memberId)
			{
				$objPlayer = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler WHERE published = ? AND id = ?')
				                                     ->execute(1, $this->User->fernschach_memberId);
				if($objPlayer->numRows)
				{
					$kontoauszug = true;

					// Salden laden
					$salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($objPlayer->id);

					if($this->fernschachverwaltung_kontostand)
					{
						// Kontostand anzeigen
						$value = end($salden);
						$wert = str_replace('.', ',', sprintf('%0.2f', $value));
						$kontostand = $wert.' €';

						if($this->fernschachverwaltung_kontostandReset)
						{
							// Resetbuchung ab 01.04.2023 erforderlich
							$checked = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkKonto($row['id']);
							if(!$checked) $kontostand = false;
						}

					}
					else
					{
						// Kontostand nicht anzeigen
						$kontostand = false;
					}

					// Buchungen einlesen
					if($salden) 
					{
						// Buchungen ausgeben
						$buchungen = array();
						$nummer = 0;
						//print_r($salden);
						foreach(array_reverse($salden, true) as $id => $value)
						{
							$objBuchung = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler_konto WHERE id = ?')
							                                      ->execute($id);
							if($objBuchung->numRows)
							{
								if($this->fernschachverwaltung_maxDatum > $objBuchung->datum) break; // Bei Ab-Datum stoppen
								$nummer++;
								$buchungen[] = array
								(
									'nummer' => $nummer,
									'datum'  => date('d.m.Y', $objBuchung->datum),
									'titel'  => $objBuchung->verwendungszweck,
									'betrag' => self::getBetrag($objBuchung->betrag, $objBuchung->typ),
									'saldo'  => self::getBetrag($value, false),
								);
								if($this->fernschachverwaltung_resetStop && $objBuchung->saldoReset) break; // Bei Saldo-Reset stoppen
								if($this->fernschachverwaltung_maxBuchungen > 0 && $this->fernschachverwaltung_maxBuchungen <= $nummer) break; // Bei Maximalanzahl Buchungen stoppen
							}
						}
					}
				}
			}
			else
			{
				$fehler = 'Kein BdF-Mitglied';
			}

		}
		else
		{  
			$fehler = 'Nicht angemeldet';
		}
		
		// Ausgabe
		$this->Template->kontoauszug = $kontoauszug;
		$this->Template->kontostand = $kontostand;
		$this->Template->buchungen = is_array($buchungen) ? $buchungen : array();
		$this->Template->fehler = $fehler;

	}

	/**
	 * Betrag formatiert zurückgeben
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public function getBetrag($value, $typ)
	{
		// Komma umwandeln in Punkt
		$value = str_replace(',', '.', $value);

		// Farbe bestimmen
		if($typ)
		{
			if($typ == 'h') $html = '';
			elseif($typ == 's') $html = '- ';
		}

		// Betrag formatieren und zurückgeben
		$value = str_replace('.', ',', sprintf('%0.2f', $value));
		return $html.$value.' €';
	}

}
