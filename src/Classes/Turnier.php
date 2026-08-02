<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\Database;

class Turnier extends Backend
{

	public function __construct()
	{
	}

	/**
	 * Funktion getNenngeld
	 * ===================================================================
	 * Gibt ein Array mit dem Nenngeld zurück. Ist im aktuellen Turnier kein Nenngeld festgelegt, wird in den übergeordneten Turnieren
	 * nach dem Nenngeld gesucht. 
	 *
	 * @param integer $turnierId     ID des Turniers
	 *
	 * @return array                 false, wenn keine Daten gefunden werden
	 *                               Ein Array mit folgenden Werten wird bei true zurückgegeben
	 *                               'parent' => true/false (übergeordnetes Turnier ja/nein)
	 *                               'id'     => ID des Turniers
	 *                               'name'   => Name des Turniers
	 *                               'amount' => Betrag in Euro
	 */
	public static function getNenngeld($turnierId)
	{
		$arr = array();
		$id = $turnierId; // ID des aktuellen Turniers zuweisen
		
		while($id > 0)
		{
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);

			if($objTurnier->published && $objTurnier->nenngeldActive) 
			{
				// Nenngeld speichern
				$arr = array
				(
					'parent'  => ($id == $turnierId) ? false : true,
					'id'      => $id,
					'name'    => $objTurnier->title,
					'amount'  => $objTurnier->nenngeld
				);
				return $arr;
			}
			$id = $objTurnier->pid; // Neue ID setzen
		}
		return false;

	}

	/*
	 * Funktion getTurnierleiter
	 * Liefert alle Turnierleiter (Name und E-Mail) eines Turniers und seiner Oberkategorien
	 */
	public static function getTurnierleiter($id)
	{
		$arr = array();
		while($id > 0)
		{
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);

			if($objTurnier->published && $objTurnier->turnierleiterInfo && $objTurnier->turnierleiterEmail)
			{
				// Turnierleiter speichern
				$arr[] = array
				(
					'name'    => $objTurnier->turnierleiterName,
					'email'   => $objTurnier->turnierleiterEmail
				);
			}
			$id = $objTurnier->pid; // Neue ID setzen
		}
		return $arr;
	}

}
