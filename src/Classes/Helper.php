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
	public static function searchMembership($value, $datum)
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
	 * Funktion isMemberBegin
	 * ======================
	 * Sucht in den Mitgliedschaften nach einem Mitgliedschaftsbeginn im übergebenen Jahr
	 *
	 * @param (ser)array $value     Serialisiertes Array mit den Mitgliedschaften
	 * @param integer    $jahr      Jahr des gesuchten Mitgliedschaftsbeginn
	 *
	 * @return boolean   true = Mitgliedschaftsbeginn gefunden / false = kein Mitgliedschaftsbeginn gefunden
	 */
	public static function isMemberBegin($value, $jahr)
	{
		$mitgliedschaften = unserialize($value); // String umwandeln

		// Mitgliedschaft in diesem Jahr suchen
		if(is_array($mitgliedschaften))
		{
			foreach($mitgliedschaften as $mitgliedschaft)
			{
				if(substr($mitgliedschaft['from'],0,4) == $jahr)
				{
					// Jahr gefunden
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Funktion searchNoMembership
	 * ===========================
	 * Sucht in den Mitgliedschaften nach der letzten Mitgliedschaft
	 *
	 * @param (ser)array $value     Serialisiertes Array mit den Mitgliedschaften
	 * @param integer    $datum     Datum des gesuchten Mitgliedsendes
	 *
	 * @return boolean   true = Letzte Mitgliedschaft endet am Datum / false = Nach dem Datum gibt es noch Mitgliedschaften
	 */
	public static function searchNoMembership($value, $datum)
	{
		$heute = date('Ymd');
		$from = 0; // Von-Datum speichern
		$to = 0; // Bis-Datum speichern
		$datum_gefunden = false; // Speichert true, wenn das gesuchte $datum gefunden wurde

		$mitgliedschaften = unserialize($value); // String umwandeln
		if(is_array($mitgliedschaften))
		{
			foreach($mitgliedschaften as $mitgliedschaft)
			{
				if($mitgliedschaft['from'] > $from) $from = $mitgliedschaft['from'];
				if($mitgliedschaft['to'] > $to) $to = $mitgliedschaft['to'];
				if($mitgliedschaft['to'] == $datum) $datum_gefunden = true;
			}
		}

		// Suchergebnis auswerten
		if($datum_gefunden)
		{
			if($from > $datum) return false; // Mitgliedschaft nach dem Datum gefunden
			else return true; // Keine Mitgliedschaft nach dem Datum
		}
		else
		{
			// Datum paßt nicht
			return false;
		}
	}

	/**
	 * Funktion Mitgliedschaft
	 *
	 * param $typ      1 = Beginn zurückgeben, 2 = Ende zurückgeben
	 * @return string
	 */
	public static function Mitgliedschaft($value, $typ)
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
	 * function getSaldo
	 * =================================================================
	 * Saldorechner für tl_fernschach_spieler_konto, tl_fernschach_spieler_konto_beitrag und tl_fernschach_spieler_konto_nenngeld
	 *
	 * @param integer $pid        ID des Spielers
	 * @param string  $konto      beitrag, nenngeld oder leer
	 * @param string  $datum      Saldo eines Datums zurückgeben, Standard (false) = aktueller Saldo
	 *
	 * @return array              Salden nach jeder Buchung nach Datum absteigend sortiert
	 */
	public static function getSaldo($pid, $konto = '', $datum = false)
	{
		$salden = array();
		$session = \Contao\Session::getInstance()->getData(); // Sitzung laden
		$sql = ''; // SQL-String Filter und Suche initialisieren

		// Filter laden
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['typ']))
		{
			if($konto)
			{
				$sql .= " AND typ = '".$session['filter']['tl_fernschach_spieler_konto_'.$konto.'_'.$pid]['typ']."'";
			}
			else $sql .= " AND typ = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['typ']."'";
		}
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['art']))
		{
			if($konto)
			{
				$sql .= " AND art = '".$session['filter']['tl_fernschach_spieler_konto_'.$konto.'_'.$pid]['art']."'";
			}
			else $sql .= " AND art = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['art']."'";
		}
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['kategorie']))
		{
			if(!$konto)
			{
				$sql .= " AND kategorie = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['kategorie']."'";
			}
		}
		if(isset($session['filter']['tl_fernschach_spieler_konto_'.$pid]['markieren']))
		{
			if($konto)
			{
				$sql .= " AND markieren = '".$session['filter']['tl_fernschach_spieler_konto_'.$konto.'_'.$pid]['markieren']."'";
			}
			else $sql .= " AND markieren = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['markieren']."'";
		}

		// Buchungen des Spielers laden
		if($konto)
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_".$konto." WHERE pid=? AND published=?".$sql.' ORDER BY datum ASC, sortierung ASC')
			                                        ->execute($pid, 1);
		}
		else
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid=? AND published=?".$sql.' ORDER BY datum ASC, sortierung ASC')
			                                        ->execute($pid, 1);
		}

		// Datum umwandeln
		if(!$datum)
		{
			$datum = date('d.m.Y');
		}
		$tag = substr($datum, 0, 2);
		$monat = substr($datum, 3, 2);
		$jahr = substr($datum, 6, 4);
		// Bugfix: $jahr+10 statt $jahr
		// Da auch Buchungen in der Zukunft eingetragen werden, werden bei $jahr nicht die Salden in der Zukunft berechnet.
		$datum_neu = mktime(23, 59, 59, $monat, $tag, $jahr+10);

		// Buchungen auswerten
		$saldo = 0;
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				// Nur Buchungen verwenden, die jünger oder gleich dem gewünschten Datum sind
				if($objBuchungen->datum <= $datum_neu)
				{
					if($objBuchungen->saldoReset || $objBuchungen->resetRecord)
					{
						$saldo = 0; // Saldo soll hier resettet werden
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
					// Saldo dem Salden-Array zuordnen
					$salden[$objBuchungen->id] = $saldo;
				}
			}
		}

		return $salden;

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
	 * function checkKonto
	 * =================================================================
	 * Sucht nach einer Resetbuchung nach dem 01.04.2023 und gibt true/false zurück
	 *
	 * @param integer $pid        ID des Spielers
	 * @param string  $konto      beitrag, nenngeld oder leer
	 *
	 * @return boolean            true/false
	 */
	public static function checkKonto($pid, $konto = '')
	{
		if($konto)
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_".$konto." WHERE pid=? AND saldoReset=? AND datum>=? AND published=?")
			                                        ->execute($pid, 1, 1680300000, 1);
		}
		else
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid=? AND saldoReset=? AND datum>=? AND published=?")
			                                        ->execute($pid, 1, 1680300000, 1);
		}

		if($objBuchungen->numRows) $resetVorhanden = true;
		else $resetVorhanden =  false;

		// Spielerdatensatz prüfen, ob accountChecked richtig gesetzt ist
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id=?")
		                                        ->execute($pid);

		if($objBuchungen->numRows)
		{
			if($konto == 'beitrag' && ($resetVorhanden != $objBuchungen->beitragChecked))
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'beitragChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($pid);
				//$this->createNewVersion('tl_fernschach_spieler', $pid);
			}
			elseif($konto == 'nenngeld' && ($resetVorhanden != $objBuchungen->nenngeldChecked))
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'nenngeldChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($pid);
				//$this->createNewVersion('tl_fernschach_spieler', $pid);
			}
			elseif($resetVorhanden != $objBuchungen->accountChecked)
			{
				// Status paßt nicht zueinander, jetzt aktualisieren
				$set = array
				(
					'accountChecked'   => $resetVorhanden,
				);
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
				                                     ->set($set)
				                                     ->execute($pid);
				//\Controller::createNewVersion('tl_fernschach_spieler', $pid);
			}
		}

		return $resetVorhanden;

	}

	/**
	 * Funktion updateResetbuchungen
	 * ============================
	 * Überprüft tl_fernschach_spieler_konto auf die Gültigkeit der globalen Resetbuchung
	 * Überprüft tl_fernschach_spieler_konto_nenngeld auf die Gültigkeit der globalen Resetbuchung
	 * Überprüft tl_fernschach_spieler_konto_beitrag auf die Gültigkeit der globalen Resetbuchung
	 */
	public function updateResetbuchungen(\DataContainer $dc)
	{
		$update = (int)$GLOBALS['TL_CONFIG']['fernschach_resetUpdate'] + $GLOBALS['TL_CONFIG']['fernschach_resetUpdate_time']; // Letztes Updatedatum + eingestellter Rhythmus

		// Aktualisierung notwendig
		if($update < time())
		{
			// Buchungen prüfen
			if($GLOBALS['TL_CONFIG']['fernschach_resetActive'])
			{
				$resetRecords = (array)unserialize($GLOBALS['TL_CONFIG']['fernschach_resetRecords']); // Reset-Datensätze einlesen

				// Alle Reset-Datensätze auswerten
				foreach($resetRecords as $resetRecord)
				{
					// Globaler Reset-Datensatz ist aktiviert
					$nummer = abs($resetRecord['nummer']);
					$typ = $resetRecord['saldo'] < 0 ? 's' : 'h';
					$betrag = abs($resetRecord['saldo']);
					$datum = abs($resetRecord['datum']);
					$konten = $resetRecord['konten'];
					if(count($konten))
					{
						foreach($konten as $konto)
						{
							self::KontoResetpruefung($konto, $nummer, $betrag, $datum, $typ);
						}
					}
				}
			}
			else
			{
				// Globaler Reset-Datensatz ist nicht aktiviert, deshalb alle Reset-Buchungen löschen
				$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE resetRecord != ?")
				                                       ->execute('');
				$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE resetRecord != ?")
				                                       ->execute('');
				$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto_beitrag WHERE resetRecord != ?")
				                                       ->execute('');
			}


			// Ja, Konfiguration aktualisieren
			\Contao\Config::persist('fernschach_resetUpdate', time()); // Siehe https://community.contao.org/de/showthread.php?83934-In-die-localconfig-php-schreiben
		}

	}

	/**
	 * Funktion KontoResetpruefung
	 * ===========================
	 * Überprüft die Konten auf Vorhandensein des Resetdatensatzes
	 * @param $konto
	 * @param $nummer
	 * @param $betrag
	 * @param $datum
	 * @param $typ
	 */
	public function KontoResetpruefung($konto, $nummer, $betrag, $datum, $typ)
	{
		switch($konto)
		{
			case 'h': $suffix = ''; break;
			case 'b': $suffix = '_beitrag'; break;
			case 'n': $suffix = '_nenngeld'; break;
			default: $suffix = '';
		}
		
		// Alle Buchungen vom ältesten bis zum jüngsten Datensatz sortiert einlesen
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$suffix." ORDER BY pid ASC, datum ASC, sortierung ASC")
		                                        ->execute();
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
				if($objBuchungen->resetRecord == $nummer)
				{
					// Reset-Datensatz gefunden
					$resetDatensaetze++;
					if($resetDatensaetze == 1 && !$juengereBuchungen && !$aeltereBuchungen)
					{
						// Reset-Datensatz löschen, da keine Buchungen davor oder danach existieren
						$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto".$suffix." WHERE id = ?")
						                                       ->execute($objBuchungen->id);
						$this->createNewVersion('tl_fernschach_spieler_konto'.$suffix, $objBuchungen->id);
					}
					elseif($resetDatensaetze > 1)
					{
						// Überflüssigen Reset-Datensatz löschen
						$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto".$suffix." WHERE id = ?")
						                                       ->execute($objBuchungen->id);
						$this->createNewVersion('tl_fernschach_spieler_konto'.$suffix, $objBuchungen->id);
					}
				}
				else
				{
					// Normaler Datensatz
					if($datum > $objBuchungen->datum) $aeltereBuchungen = true;
					if($datum < $objBuchungen->datum) $juengereBuchungen = true;
					if($aeltereBuchungen && $juengereBuchungen && !$resetDatensaetze)
					{
						// Reset-Buchung anlegen
						$set = array
						(
							'pid'              => $objBuchungen->pid,
							'tstamp'           => time(),
							'resetRecord'      => $nummer,
							'betrag'           => $betrag,
							'datum'            => $datum,
							'saldoReset'       => 1,
							'typ'              => $typ,
							'verwendungszweck' => 'Saldo global neu gesetzt',
						);
						$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto".$suffix." %s")
						                                     ->set($set)
						                                     ->execute();
						$resetDatensaetze++;
					}
				}
			}
		}
	}

	/**
	 * Funktion updateMitgliedschaften
	 * ============================
	 * Überprüft tl_fernschach_spieler auf die Gültigkeit des Mitgliedsstatus
	 */
	public function updateMitgliedschaften(\DataContainer $dc)
	{
		$update = (int)$GLOBALS['TL_CONFIG']['fernschach_membershipUpdate'] + $GLOBALS['TL_CONFIG']['fernschach_membershipUpdate_time']; // Letztes Updatedatum + eingestellter Rhythmus

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
						$this->createNewVersion('tl_fernschach_spieler_konto', $objResets->id);
						$resetDatensaetze++;
					}
					elseif($resetDatensaetze > 1)
					{
						// Überflüssige Reset-Buchung löschen
						$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
						                                       ->execute($objResets->id);
						$this->createNewVersion('tl_fernschach_spieler_konto', $objResets->id);
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
					$this->createNewVersion('tl_fernschach_spieler', $objBuchungen->id);
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
							$this->createNewVersion('tl_fernschach_spieler_konto', $objBuchungen->id);
						}
					}
					else
					{
						// Reset-Datensatz löschen, da unerwünscht
						$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
						                                       ->execute($objBuchungen->id);
						$this->createNewVersion('tl_fernschach_spieler_konto', $objBuchungen->id);
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
	public static function getSpieler($id = false, $feld = false)
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
						'vorname'      => $objSpieler->vorname,
						'nachname'     => $objSpieler->nachname,
						'memberId'     => $objSpieler->memberId,
						'sepaNenngeld' => $objSpieler->sepaNenngeld,
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
	public static function getSpielerdatensatz($id = false, $member = false)
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
	public static function getTurnierdatensatz($id)
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

	/**
	 * Sucht für eine Spieler-ID alle Anmeldungen und Bewerbungen und gibt diese absteigend sortiert nach Meldedatum zurück
	 * @param
	 * @return    object
	 */
	public static function getAnmeldungenBewerbungen($id)
	{
		// Link-Prefixe generieren, ab C4 ist das ein symbolischer Link zu "contao"
		if(version_compare(VERSION, '4.0', '>='))
		{
			$linkprefix = \System::getContainer()->get('router')->generate('contao_backend');
			$imageEdit = \Image::getHtml('edit.svg', 'Bewerbung des Mitglieds bearbeiten');
		}
		else
		{
			$linkprefix = 'contao/main.php';
			$imageEdit = \Image::getHtml('edit.gif', 'Bewerbung des Mitglieds bearbeiten');
		}

		$objAnmeldungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE spielerId = ?")
		                                          ->execute($id);
		$objBewerbungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_bewerbungen WHERE spielerId = ?")
		                                          ->execute($id);

		// Datensätze zusammenfassen
		$records = array();
		if($objAnmeldungen->numRows)
		{
			while($objAnmeldungen->next())
			{
				$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($objAnmeldungen->pid);
				$records[] = array
				(
					'typ'        => 'Anmeldung',
					'datum'      => $objAnmeldungen->meldungDatum,
					'turnier'    => $objTurnier ? $objTurnier->title : '',
					'status'     => 0,
					'id'         => $objAnmeldungen->id,
					'link'       => '<a href="'.$linkprefix.'?do=fernschach-turniere&amp;table=tl_fernschach_turniere_meldungen&amp;act=edit&amp;id='.$objAnmeldungen->id.'&amp;popup=1&amp;rt='.REQUEST_TOKEN.'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>'
				);
			}
		}
		if($objBewerbungen->numRows)
		{
			while($objBewerbungen->next())
			{
				$objTurnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($objAnmeldungen->pid);
				$records[] = array
				(
					'typ'        => 'Bewerbung',
					'datum'      => $objBewerbungen->applicationDate,
					'turnier'    => $objTurnier ? $objTurnier->title : '',
					'status'     => 0,
					'id'         => $objBewerbungen->id,
					'link'       => '<a href="'.$linkprefix.'?do=fernschach-turniere&amp;table=tl_fernschach_turniere_bewerbungen&amp;act=edit&amp;id='.$objBewerbungen->id.'&amp;popup=1&amp;rt='.REQUEST_TOKEN.'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>'
				);
			}
		}

		// Liste sortieren
		if($records) $records = \Schachbulle\ContaoHelperBundle\Classes\Helper::sortArrayByFields($records, array('datum' => SORT_DESC));

		// Laufende Nummer hinzufügen
		$max = count($records);
		$akt = $max;
		for($x = 0; $x < $max; $x++)
		{
			$records[$x]['nummer'] = $akt;
			$akt--;
		}

		return $records;
	}

}
