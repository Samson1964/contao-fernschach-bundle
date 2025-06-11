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

		$kontoauszug = false;
		$kontostand = false;
		$saldo = false;
		$fehler = '';

		// FE-Mitglied in Fernschach-Verwaltung suchen, wenn ein FE-Mitglied angemeldet ist
		if($this->User->id)
		{
			if($this->User->fernschach_memberId)
			{
				$objPlayer = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler WHERE published=? AND id=?')
				                                     ->execute(1, $this->User->fernschach_memberId);
				if($objPlayer->numRows)
				{
					// Muß Resetbuchung ab 01.04.2023 vorhanden sein? Falls Ja, dann danach suchen
					if($this->fernschachverwaltung_isReset)
					{
						// Resetbuchung erforderlich
						$reset = new \Schachbulle\ContaoFernschachBundle\Classes\Konto\Resetbuchung_2023($objPlayer->id);
						$resetGefunden = $reset->getResetbuchung();
						if($resetGefunden)
						{
							// Resetbuchung gefunden, Kontostand und Kontoauszug darf angezeigt werden
							$kontoauszug = true;
							$kontostand = $this->fernschachverwaltung_kontostand ? true : false;
						}
					}
					else
					{
						// Resetbuchung nicht erforderlich
						$kontoauszug = true;
						$kontostand = $this->fernschachverwaltung_kontostand ? true : false;
					}

					$konten = (array)unserialize($this->fernschachverwaltung_konten);

					$kontoauszug = array();
					foreach($konten as $konto)
					{
						$buchungen = self::getKonto($konto, $objPlayer, $kontostand);
						if($konto == 'h' && $this->fernschachverwaltung_hauptkonto && $buchungen['saldo_raw'] == 0)
						{
							// Nichts machen, da Hauptkonto bei Saldo = 0 ausgeblendet werden soll
						}
						else
						{
							// Kontoauszug ausgeben
							$arrReturn[$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_konten_options'][$konto]] = $buchungen;
							$kontoauszug = array_merge($kontoauszug, $arrReturn);
						}
					}
				}
				// Ausgabe
				$this->Template->kontoauszug = $kontoauszug;
				$this->Template->kontostand = $kontostand;
				$this->Template->sepaNenngeld = $objPlayer->sepaNenngeld;
				$this->Template->sepaBeitrag = $objPlayer->sepaBeitrag;
			}
			else
			{
				$fehler = $GLOBALS['TL_CONFIG']['fernschach_hinweis_kontoauszug'];
			}

		}
		else
		{
			$fehler = 'Nicht angemeldet';
		}

		// Ausgabe ergänzen
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
		$html = '';

		// Farbe bestimmen
		if($typ)
		{
			if($typ == 'h') $html = '';
			elseif($typ == 's') $html = '-';
		}

		// Betrag formatieren und zurückgeben
		$value = str_replace('.', ',', sprintf('%0.2f', $value));
		return $html.$value.' €';
	}

	/**
	 * Gibt die Informationen zum Konto zurück: Saldo und Auszug
	 *
	 * @param string $typ         h, b, n (Hauptkonto, Beitragskonto, Nenngeldkonto)
	 *
	 * @return array
	 */
	public function getKonto($typ, $spieler, $kontostand)
	{

		$buchungen = array();

		switch($typ)
		{
			case 'h': $kontoname = ''; break;
			case 'b': $kontoname = '_beitrag'; break;
			case 'n': $kontoname = '_nenngeld'; break;
		}

		// Salden laden
		$salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($spieler->id, $kontoname);

		if($kontostand)
		{
			// Kontostand anzeigen
			$value = end($salden);
			$wert = str_replace('.', ',', sprintf('%0.2f', $value));
			$saldo = $wert.' €';
			$saldo_raw = $value;
		}
		else $saldo = false; // Kontostand nicht anzeigen

		// Buchungen einlesen
		if($salden)
		{
			// Buchungen ausgeben
			$buchungen = array();
			$nummer = 0;
			//print_r($salden);
			foreach(array_reverse($salden, true) as $id => $value)
			{
				$objBuchung = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler_konto'.$kontoname.' WHERE id=? AND published=?')
				                                      ->execute($id, 1);
				if($objBuchung->numRows)
				{
					if($this->fernschachverwaltung_maxDatum > $objBuchung->datum) break; // Bei Ab-Datum stoppen
					$nummer++;
					$buchungen[] = array
					(
						'nummer'     => $nummer,
						'datum'      => date('d.m.Y', $objBuchung->datum),
						'titel'      => $objBuchung->verwendungszweck,
						'betrag'     => str_replace(' ', '&nbsp;', self::getBetrag($objBuchung->betrag, $objBuchung->typ)),
						'saldo'      => $kontostand ? str_replace(' ', '&nbsp;', self::getBetrag($value, false)) : '',
					);
					if($this->fernschachverwaltung_isReset && $objBuchung->saldoReset) break; // Bei Saldo-Reset stoppen
					if($this->fernschachverwaltung_maxBuchungen > 0 && $this->fernschachverwaltung_maxBuchungen <= $nummer) break; // Bei Maximalanzahl Buchungen stoppen
				}
			}
		}

		$kontoname = $GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_konten_options'][$typ];

		return array
		(
			'saldo'     => $saldo,
			'saldo_raw' => $saldo_raw,
			'buchungen' => is_array($buchungen) ? $buchungen : array()
		);
	}

}
