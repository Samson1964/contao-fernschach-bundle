<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Konto;

/**
 * Class Nenngeld
  */
class Nenngeld extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Sucht nach Nenngeld-Konten mit einem negativem Saldo, addiert diese und gibt Anzahl und Gesamtsumme zurück
	 * Es werden alle veröffentlichten Spieler und zusätzlich nur bei Mitgliedern geprüft 
	 */
	public static function getNegativ()
	{
		$anzahl_alle = 0;
		$summe_alle = 0;
		$anzahl_mitglieder = 0;
		$summe_mitglieder = 0;

		$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE published = ?")
		                                      ->execute(1);

		if($objSpieler->numRows)
		{
			while($objSpieler->next())
			{
				$salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($objSpieler->id, 'nenngeld', false, false);
				$nenngeldsaldo = end($salden);
				if($nenngeldsaldo < 0)
				{
					$anzahl_alle++;
					$summe_alle += $nenngeldsaldo;
					// Mitgliedsstatus prüfen
					$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objSpieler->memberships);
					if($mitglied)
					{
						$anzahl_mitglieder++;
						$summe_mitglieder += $nenngeldsaldo;
					}
				}
			}
		}

		return array
		(
			'anzahl_alle'       => $anzahl_alle,
			'summe_alle'        => $summe_alle,
			'anzahl_mitglieder' => $anzahl_mitglieder,
			'summe_mitglieder'  => $summe_mitglieder,
		);

	}

}
