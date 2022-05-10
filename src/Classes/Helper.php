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
	public function getSpieler()
	{
		static $spieler;
		
		if(!$spieler);
		{
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler ORDER BY nachname ASC, vorname ASC")
			                                      ->execute();
        	
			$spieler = array();
			if($objSpieler->numRows)
			{
				while($objSpieler->next())
				{
					$spieler[$objSpieler->id] = $objSpieler->vorname.' '.$objSpieler->nachname.' ('.$objSpieler->memberId.')';
				}
			}
		}
		return $spieler;
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
