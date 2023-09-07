<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class Helper extends \Backend
{

	var $spieler = array();

	public function __construct()
	{

	}


	/**
	 * Funktion checkMembership
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public static function checkMembership($value, $heute = false)
	{
		if(!$heute) $heute = date('Ymd');
		
		$mitgliedschaften = unserialize($value); // String umwandeln
		//print_r($mitgliedschaften);
		$return = false;
		if(is_array($mitgliedschaften))
		{
			//print_r($mitgliedschaften);
			foreach($mitgliedschaften as $mitgliedschaft)
			{
				if($mitgliedschaft['from'] == 0 && $mitgliedschaft['to'] == 0)
				{
					// Leerer Datensatz (wird nicht berücksichtigt)
				}
				elseif($mitgliedschaft['from'] > 0 && $mitgliedschaft['to'] > 0)
				{
					// Beendete Mitgliedschaft
					if($mitgliedschaft['from'] <= $heute && $mitgliedschaft['to'] >= $heute)
					{
						// Mitgliedschaft zum Zeitpunkt von $heute gefunden
						//echo 'OK '.$heute.'<br>';
						return true;
					}
				}
				elseif($mitgliedschaft['from'] == 0 || $mitgliedschaft['from'] <= $heute)
				{
					// Beginndatum nicht gesetzt oder kleiner/gleich aktuellem Tag, also möglicherweise Mitglied
					if($mitgliedschaft['to'] == 0 || $mitgliedschaft['to'] > $heute)
					{
						// Endedatum nicht gesetzt oder größer aktuellem Tag, also Mitglied
						//echo 'OK '.$heute.'<br>';
						return true;
					}
				}
			}
		}
		//echo 'ERROR '.$heute.'<br>';
		return $return;
	}

	/**
	 * Funktion getAlter
	 *
	 * @param integer $birthday      Geburtsdatum im Format JJJJMMTT
	 * @param integer $datum         Datum (für Ermittlung des Alters) im Format JJJJMMTT
	 *
	 * @return string
	 */
	public static function getAlter($birthday, $datum = false)
	{
		if(!$datum) $datum = date('Ymd');
		
		try 
		{
			if($birthday)
			{
				$datum1 = new \DateTime(substr($birthday, 0, 4).'-'.substr($birthday, 4, 2).'-'.substr($birthday, 6, 2)); // Geburtsdatum im Format JJJJ-MM-TT
				$datum2 = new \DateTime(substr($datum, 0, 4).'-'.substr($datum, 4, 2).'-'.substr($datum, 6, 2)); // Altersdatum im Format JJJJ-MM-TT
				$interval = $datum2->diff($datum1);
				return $interval->format("%Y");
			}
			else
			{
				return 0;
			}
		}
		catch(Exception $e)
		{
			return 0;
		}

	}

	/**
	 * Funktion searchMembership
	 *
	 * @param integer $value
	 * @param integer $datum     Datum des Mitgliedsendes
	 *
	 * @return string
	 */
	public function searchMembership($value, $datum)
	{
		$heute = date('Ymd');
		$mitgliedschaften = unserialize($value); // String umwandeln
		if(is_array($mitgliedschaften))
		{
			foreach($mitgliedschaften as $mitgliedschaft)
			{
				if($mitgliedschaft['from'] == 0 && $mitgliedschaft['to'] == 0)
				{
					// Leerer Datensatz (wird nicht berücksichtigt)
				}
				elseif($mitgliedschaft['to'] == $datum)
				{
					// Datum gefunden
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Funktion Mitgliedschaft
	 *
	 * param $typ      1 = Beginn zurückgeben, 2 = Ende zurückgeben
	 * @return string
	 */
	public function Mitgliedschaft($value, $typ)
	{
		$heute = date('Ymd');
		$mitgliedschaften = unserialize($value); // String umwandeln
		$return = false;
		$beginn = 0;
		$ende = 0;
		if(is_array($mitgliedschaften))
		{
			foreach($mitgliedschaften as $mitgliedschaft)
			{
				if($mitgliedschaft['from'] > $beginn)
				{
					// Aktueller Mitgliedsbeginn ist größer als der ältere Mitgliedsbeginn, darum komplett übernehmen
					$beginn = $mitgliedschaft['from'];
					$ende = $mitgliedschaft['to'];
				}
			}
		}
		if($typ == 1) return $beginn;
		elseif($typ == 2) return $ende;
		else return false;
	}

	/**
	 * Saldorechner
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public static function getSaldo($pid)
	{
		$salden = array();
		$session = \Contao\Session::getInstance()->getData(); // Sitzung laden
		$sql = ''; // SQL-String Filter und Suche initialisieren

		// Filter laden
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['typ']))
		{
			$sql .= " AND typ = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['typ']."'";
		}
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['art']))
		{
			$sql .= " AND art = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['art']."'";
		}
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['kategorie']))
		{
			$sql .= " AND kategorie = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['kategorie']."'";
		}
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['markieren']))
		{
			$sql .= " AND markieren = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['markieren']."'";
		}

		//echo "<pre>";
		// Filter laden

		//print_r($session['filter']['tl_fernschach_spieler_konto_'.$pid]); // typ =>, art =>
		//print_r($session['search']['tl_fernschach_spieler_konto']); // field => name, value =>
		//print_r($session);
		//echo "</pre>";
		//echo $sql;
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid=? AND published=?".$sql.' ORDER BY datum ASC, sortierung ASC')
		                                        ->execute($pid, 1);

		$saldo = 0;
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				//echo "Betrag=".$objBuchungen->betrag." Saldo davor=".$saldo;
				if($objBuchungen->saldoReset || $objBuchungen->resetRecord)
				{
					$saldo = 0; // Saldo soll hier resettet werden
					//echo " Saldo nach Reset=".$saldo;
				}
				switch($objBuchungen->typ)
				{
					case 'h':
						$saldo += (float)$objBuchungen->betrag;
						break;
					case 's':
						$saldo -= (float)$objBuchungen->betrag;
						break;
					default:
				}
				//echo " Saldo danach=".$saldo."<br>";
				// Saldo dem Salden-Array zuordnen
				$salden[$objBuchungen->id] = $saldo;
			}
		}

		//print_r($salden);
		return $salden;
		// Saldo formatieren
		//echo $saldo;
		//if((float)$saldo >= 0) return '<span style="color:green;">'.self::getEuro($saldo).'</span>';
		//else return '<span style="color:red;">'.self::getEuro($saldo).'</span>';

	}

	/**
	 * Set the timestamp to 00:00:00 (see #26)
	 *
	 * @param integer $value
	 *
	 * @return integer
	 */
	public function loadDate($value)
	{
		if($value) return strtotime(date('Y-m-d', $value) . ' 00:00:00');
		else return '';
	}

	/**
	 * checkKonto
	 * Sucht nach einer Resetbuchung nach dem 01.04.2023 und gibt true/false zurück 
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public static function checkKonto($pid)
	{
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid=? AND saldoReset=? AND datum>=? AND published=?")
		                                        ->execute($pid, 1, 1680300000, 1);

		if($objBuchungen->numRows) $resetVorhanden = true;
		else $resetVorhanden =  false;

		// Spielerdatensatz prüfen, ob accountChecked richtig gesetzt ist
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id=?")
		                                        ->execute($pid);

		if($objBuchungen->numRows)
		{
			if($resetVorhanden != $objBuchungen->accountChecked)
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'accountChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($pid);
				//$this->createNewVersion('tl_fernschach_spieler', $pid);
			}
		}
		
		return $resetVorhanden;

	}

	/**
	 * Funktion updateResetbuchungen
	 * ============================
	 * Überprüft tl_fernschach_spieler_konto auf die Gültigkeit der globalen Resetbuchung
	 */
	public function updateResetbuchungen(\DataContainer $dc)
	{
		$update = (int)$GLOBALS['TL_CONFIG']['fernschach_resetUpdate'] + 86400; // Letztes Updatedatum + 1 Tag

		// Aktualisierung notwendig
		if($update < time())
		{
			// Buchungen prüfen
			if(isset($GLOBALS['TL_CONFIG']['fernschach_resetActive']))
			{
				// Globaler Reset-Datensatz ist aktiviert
				$typGlobal = $GLOBALS['TL_CONFIG']['fernschach_resetSaldo'] < 0 ? 's' : 'h';
				$betragGlobal = abs($GLOBALS['TL_CONFIG']['fernschach_resetSaldo']);
				$datumGlobal = abs($GLOBALS['TL_CONFIG']['fernschach_resetDate']);

				// Alle Buchungen vom ältesten bis zum jüngsten Datensatz sortiert einlesen
				$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto ORDER BY pid ASC, datum ASC, sortierung ASC")
				                                        ->execute($playerId);
				if($objBuchungen->numRows)
				{
					$pid = 0; // Letzte Spieler-ID merken
					while($objBuchungen->next())
					{
						if($objBuchungen->pid != $pid)
						{
							// Neuer Spieler, deshalb zuerst Variablen zurücksetzen
							$resetDatensaetze = 0; // Bisher gefundene Datensätze speichern
							$juengereBuchungen = false; // Jüngere Buchungen vorhanden
							$aeltereBuchungen = false; // Ältere Buchungen vorhanden
							$pid = $objBuchungen->pid; // Neuen Spieler der pid zuordnen
						}
						// Datensatz untersuchen
						if($objBuchungen->resetRecord)
						{
							// Reset-Datensatz gefunden
							$resetDatensaetze++;
							if($resetDatensaetze == 1 && !$juengereBuchungen && !$aeltereBuchungen)
							{
								// Reset-Datensatz löschen, da keine Buchungen davor oder danach existieren
								$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
								                                       ->execute($objBuchungen->id);
							}
							elseif($resetDatensaetze > 1)
							{
								// Überflüssigen Reset-Datensatz löschen
								$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
								                                       ->execute($objBuchungen->id);
							}
						}
						else
						{
							// Normaler Datensatz
							if($datumGlobal > $objBuchungen->datum) $aeltereBuchungen = true;
							if($datumGlobal < $objBuchungen->datum) $juengereBuchungen = true;
							if($aeltereBuchungen && $juengereBuchungen && !$resetDatensaetze)
							{
								// Reset-Buchung anlegen
								$set = array
								(
									'pid'              => $objBuchungen->pid,
									'tstamp'           => time(),
									'resetRecord'      => 1,
									'betrag'           => $betragGlobal,
									'datum'            => $datumGlobal,
									'saldoReset'       => 1,
									'typ'              => $typGlobal,
									'verwendungszweck' => 'Saldo global neu gesetzt',
								);
								$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto %s")
								                                     ->set($set)
								                                     ->execute();
								$resetDatensaetze++;
							}
						}
					}
				}
			}
			else
			{
				// Globaler Reset-Datensatz ist nicht aktiviert, deshalb alle Reset-Buchungen löschen
				$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE resetRecord = ?")
				                                       ->execute(1);
			}


			// Ja, Konfiguration aktualisieren
			\Contao\Config::persist('fernschach_resetUpdate', time()); // Siehe https://community.contao.org/de/showthread.php?83934-In-die-localconfig-php-schreiben
		}
		
	}

	/**
	 * Funktion updateMitgliedschaften
	 * ============================
	 * Überprüft tl_fernschach_spieler auf die Gültigkeit des Mitgliedsstatus
	 */
	public function updateMitgliedschaften(\DataContainer $dc)
	{
		$update = (int)$GLOBALS['TL_CONFIG']['fernschach_membershipUpdate'] + 86400; // Letztes Updatedatum + 1 Tag

		// Aktualisierung notwendig
		if($update < time())
		{
			// Spieler suchen mit Status 1 (Mitglied) oder 2 (Ausgetreten)
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE status = ? OR status = ?")
			                                      ->execute(1, 2);
			if($objSpieler->numRows)
			{
				// Spieler prüfen
				while($objSpieler->next())
				{
					$mitglied = self::checkMembership($objSpieler->memberships);
					if(($mitglied && $objSpieler->status == 2) || (!$mitglied && $objSpieler->status == 1))
					{
						// Mitgliedschaft und Status weichen voneinander ab
						// Datensatz aktualisieren
						if($objSpieler->status == 1)
						{
							$set = array
							(
								'status' => 2
							);
						}
						elseif($objSpieler->status == 2)
						{
							$set = array
							(
								'status' => 1
							);
						}
						\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
						                        ->set($set)
						                        ->execute($objSpieler->id);
						$this->createNewVersion('tl_fernschach_spieler', $objSpieler->id);
						
						// System-Log schreiben
						$text = 'Fernschach-Verwaltung: Mitgliedsstatus '.$objSpieler->nachname.', '.$objSpieler->vorname.' automatisch korrigiert:';
						if($objSpieler->status == 1) $text .= ' Mitglied -> Ausgetreten';
						elseif($objSpieler->status == 2) $text .= ' Ausgetreten -> Mitglied';
						\System::getContainer()->get('monolog.logger.contao')
						                       ->log(\Psr\Log\LogLevel::INFO, $text, array('contao' => new \Contao\CoreBundle\Monolog\ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_GENERAL)));
						
					}
				}
			}

			// Ja, Konfiguration aktualisieren
			\Contao\Config::persist('fernschach_membershipUpdate', time()); // Siehe https://community.contao.org/de/showthread.php?83934-In-die-localconfig-php-schreiben
		}
		
	}

	/**
	 * Funktion checkResetbuchungen
	 * ============================
	 * Sucht in den Buchungen eines Spielers nach globalen Reset-Buchungen, prüft und aktualisiert diese
	 * @param $id        ID des Spielers
	 * @return           Keine Rückgabe. Es wird direkt in die Datenbank geschrieben
	 */
	public function checkResetbuchungen($playerId)
	{
		$BuchungenJuenger = false; // Boolean, um festzustellen das es jüngere Buchungen als Reset gibt
		$BuchungenAelter = false; // Boolean, um festzustellen das es ältere Buchungen als Reset gibt
		$resetDatensaetze = 0; // Zähler, um festzustellen wieviel Reset-Datensätze existieren. Erlaubt ist max. 1
		
		// Reset-Datensatz-Werte setzen
		if($GLOBALS['TL_CONFIG']['fernschach_resetActive'])
		{
			$typGlobal = $GLOBALS['TL_CONFIG']['fernschach_resetSaldo'] < 0 ? 's' : 'h';
			$betragGlobal = abs($GLOBALS['TL_CONFIG']['fernschach_resetSaldo']);
			$datumGlobal = abs($GLOBALS['TL_CONFIG']['fernschach_resetDate']);

			// Reset-Buchungen suchen
			$objResets = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid = ? AND resetRecord = ?")
			                                     ->execute($id, 1);
			if($objResets->numRows)
			{
				// Reset-Buchung ggfs. aktualisieren
				while($objResets->next())
				{
					$resetDatensaetze++;
					if($datumGlobal != $objResets->datum || $betragGlobal != $objResets->betrag || $typGlobal != $objResets->typ && $resetDatensaetze == 1)
					{
						// Unterschied gefunden, dann aktualisieren
						$set = array
						(
							'tstamp'           => time(),
							'betrag'           => $betragGlobal,
							'datum'            => $datumGlobal,
							'typ'              => $typGlobal,
						);
						$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
						                                     ->set($set)
						                                     ->execute($objResets->id);
						$resetDatensaetze++;
					}
					elseif($resetDatensaetze > 1)
					{
						// Überflüssige Reset-Buchung löschen
						$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
						                                       ->execute($objResets->id);
					}
				}
			}
		}

		// Alle Buchungen des Spielers vom ältesten bis zum jüngsten Datensatz sortiert einlesen
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid = ? ORDER BY datum ASC, sortierung ASC")
		                                        ->execute($playerId);
		if($objBuchungen->numRows)
		{
			$resetDatensaetze = 0;
			while($objBuchungen->next())
			{
				if($objBuchungen->resetRecord && !$BuchungenJuenger && !$BuchungenAelter)
				{
					// Reset-Datensatz hier unnötig, da es keine jüngeren oder älteren Buchungen gibt -> also löschen
					$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
					                                       ->execute($objBuchungen->id);
				}
				elseif($objBuchungen->resetRecord && $BuchungenJuenger && $BuchungenAelter)
				{
					$resetDatensaetze++;
					// Reset-Datensatz gefunden, und es gibt jüngeren oder älteren Buchungen -> also aktualisieren/löschen
					if($GLOBALS['TL_CONFIG']['fernschach_resetActive'])
					{
						if($datumGlobal != $objBuchungen->datum || $betragGlobal != $objBuchungen->betrag || $typGlobal != $objBuchungen->typ)
						{
							// Unterschied gefunden, dann aktualisieren
							$set = array
							(
								'tstamp'           => time(),
								'betrag'           => $betragGlobal,
								'datum'            => $datumGlobal,
								'typ'              => $typGlobal,
							);
							$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
							                                     ->set($set)
							                                     ->execute($objBuchungen->id);
						}
					}
					else
					{
						// Reset-Datensatz löschen, da unerwünscht
						$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
						                                       ->execute($objBuchungen->id);
					}
				}
				elseif(!$objBuchungen->resetRecord && $BuchungenJuenger && $BuchungenAelter && !$resetDatensaetze && $GLOBALS['TL_CONFIG']['fernschach_resetActive'])
				{
					// Reset-Buchung anlegen
					$set = array
					(
						'pid'              => $playerId,
						'tstamp'           => time(),
						'resetRecord'      => 1,
						'betrag'           => $betragGlobal,
						'datum'            => $datumGlobal,
						'saldoReset'       => 1,
						'typ'              => $typGlobal,
						'verwendungszweck' => 'Saldo global neu gesetzt',
					);
					$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto %s")
					                                     ->set($set)
					                                     ->execute();
					$resetDatensaetze++;
				}
				elseif(!$objBuchungen->resetRecord)
				{
					// Normaler Datensatz, Buchungsdatum vergleichen mit Resetdatum
					if($GLOBALS['TL_CONFIG']['fernschach_resetActive'])
					{
						if($datumGlobal > $objBuchungen->datum) $BuchungenAelter = true;
						if($datumGlobal < $objBuchungen->datum) $BuchungenJuenger = true;
					}
				}
			}
		}
		else
		{
			// Keine Buchungen gefunden
		}
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
