<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Konto;

class Resetbuchung_2023 extends \Backend
{

	var $konto = '';
	var $spieler = 0;
	var $zeitstempel = 1680300000; // 01.04.2023
	
	/**
	 * function __construct
	 * =================================================================
	 * @param integer $id         ID des Spielers
	 * @param string  $konto      b, n oder h (Beitrag-, Nenngeld- oder Hauptkonto)
	 */
	public function __construct($id, $konto = '')
	{
		$this->spieler = $id;
		switch($konto)
		{
			case 'h': $this->konto = ''; break;
			case 'b': $this->konto = '_beitrag'; break;
			case 'n': $this->konto = '_nenngeld'; break;
			default: $this->konto = '';
		}
		//self::getResetbuchung();
	}

	/**
	 * function getResetbuchung
	 * =================================================================
	 * Sucht nach einer Resetbuchung nach dem 01.04.2023 und gibt true/false zurück
	 *
	 * @param integer $id         ID des Spielers
	 * @param string  $konto      b, n oder h (Beitrag-, Nenngeld- oder Hauptkonto)
	 *
	 * @return boolean            true/false
	 */
	public function getResetbuchung()
	{
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$this->konto." WHERE pid=? AND saldoReset=? AND datum>=? AND published=? AND resetRecord=?")
		                                        ->execute($this->spieler, 1, $this->zeitstempel, 1, '');

		if($objBuchungen->numRows) $resetVorhanden = true;
		else $resetVorhanden =  false;

		// Spielerdatensatz prüfen, ob accountChecked richtig gesetzt ist
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id=?")
		                                        ->execute($this->spieler);

		if($objBuchungen->numRows)
		{
			if($this->konto == '_beitrag' && ($resetVorhanden != $objBuchungen->accountChecked))
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'accountChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($this->spieler);
				\Controller::createNewVersion('tl_fernschach_spieler', $this->spieler);
			}
			elseif($this->konto == '_nenngeld' && ($resetVorhanden != $objBuchungen->accountChecked))
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'beitragChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($this->spieler);
				\Controller::createNewVersion('tl_fernschach_spieler', $this->spieler);
			}
			elseif($resetVorhanden != $objBuchungen->accountChecked)
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'nenngeldChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($this->spieler);
				\Controller::createNewVersion('tl_fernschach_spieler', $this->spieler);
			}
		}

		return $resetVorhanden;

	}

}
