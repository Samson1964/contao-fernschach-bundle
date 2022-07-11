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
			// Bestimmten Spieler zurückgeben
			if($feld)
			{
				// Bestimmtes Feld zurückgeben
				return $spieler[$id][$feld];
			}
			else
			{
				// Alle Felder zurückgeben
				return $spieler[$id];
			}
		}
		else
		{
			// Alle Spieler zurückgeben
			return $spieler;
		}
	}

	/**
	 * Spielerdatensatz anhand ID oder Mitgliedsnummer aus tl_fernschach_spieler laden
	 * @param
	 * @return    object
	 */
	public function getSpielerdatensatz($id = false, $member = false)
	{
		if($id)
		{
			// Suche anhand ID
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
			                                      ->execute($id);
			return $objSpieler;
		}

		if($member)
		{
			// Suche anhand Mitgliedsnummer
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE memberId = ?")
			                                      ->limit(1)
			                                      ->execute($member);
			return $objSpieler;
		}

		return false;
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

	/**
	 * Turnierdatensatz anhand ID tl_fernschach_turniere laden
	 * @param
	 * @return    object
	 */
	public function getTurnierdatensatz($id)
	{
		if($id)
		{
			// Suche anhand ID
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);
			return $objTurnier;
		}
		return false;
	}

}
