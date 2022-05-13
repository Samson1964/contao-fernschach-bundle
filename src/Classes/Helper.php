<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class Helper extends \Backend
{

	var $spieler = array();

	public function __construct()
	{

	}

	/**
	 * Spielernamen (id = Index) aus tl_fernschach_spieler laden
	 * @param
	 * @return    array
	 */
	public function getSpieler($id = false, $feld = false)
	{
		static $spieler;

		// Spielerdaten laden, wenn noch nicht geschehen
		if(!$spieler);
		{
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler ORDER BY nachname ASC, vorname ASC")
			                                      ->execute();

			$spieler = array();
			if($objSpieler->numRows)
			{
				while($objSpieler->next())
				{
					$spieler[$objSpieler->id] = array
					(
						'vorname'  => $objSpieler->vorname,
						'nachname' => $objSpieler->nachname,
						'memberId' => $objSpieler->memberId,
					);
				}
			}
		}
		
		if($id)
		{
			// Bestimmten Spieler zur�ckgeben
			if($feld)
			{
				// Bestimmtes Feld zur�ckgeben
				return $spieler[$id][$feld];
			}
			else
			{
				// Alle Felder zur�ckgeben
				return $spieler[$id];
			}
		}
		else
		{
			// Alle Spieler zur�ckgeben
			return $spieler;
		}
	}

	/**
	 * Spielernamen (id = Index) aus tl_fernschach_spieler laden
	 * @param
	 * @return    array
	 */
	public function getMeldungen()
	{
		static $spieler;

		if(!$spieler);
		{
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen ORDER BY nachname ASC, vorname ASC")
			                                      ->execute();

			$spieler = array();
			if($objSpieler->numRows)
			{
				while($objSpieler->next())
				{
					$spieler[$objSpieler->id] = $objSpieler->vorname.' '.$objSpieler->nachname;
				}
			}
		}
		return $spieler;
	}

}
