<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\Config;
use Contao\DataContainer;
use Contao\Database;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;

class Helper extends Backend
{

	var $spieler = array();

	public function __construct()
	{
	}

	/**
	 * Liefert die noch nicht genutzten Qualifikationen eines Spielers.
	 *
	 * Berücksichtigt werden nur Qualifikationen, die weder für ein Turnier
	 * verwendet noch angemeldet wurden.
	 *
	 * @param object $playerRecord Spielerdatensatz aus tl_fernschach_spieler
	 *
	 * @return array|false Liste der offenen Qualifikationen, oder false, wenn der
	 *                     Spieler nicht veröffentlicht ist
	 */
	public static function getQualifikationen($playerRecord)
	{
		if(!$playerRecord->published) return false; // Datensatz nicht veröffentlicht

		$qualifikationen = StringUtil::deserialize($playerRecord->qualifikationen); // String umwandeln
		$return = array();

		if(is_array($qualifikationen))
		{
			foreach($qualifikationen as $qualifikation)
			{
				if(!$qualifikation['genutzt_fuer'] && !$qualifikation['angemeldet_am'])
				{
					$return[] = array
					(
						'fuer'          => $qualifikation['fuer'],
						'im_turnier'    => $qualifikation['im_turnier'],
						'vom'           => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($qualifikation['vom']),
						'gueltig_bis'   => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($qualifikation['gueltig_bis']),
						'genutzt_fuer'  => $qualifikation['genutzt_fuer'],
						'angemeldet_am' => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($qualifikation['angemeldet_am']),
					);
				}
			}
		}
		return $return;
	}

	/**
	 * Funktion checkMembership
	 * ==================================================================
	 * Liefert den Status der BdF-Mitgliedschaft zurück: true oder false
	 * @param object $playerRecord    Spielerdatensatz
	 * @param integer $heute          Referenzdatum (optional) in der Form JJJJMMTT (Standard: aktuelles Datum)
	 * @param boolean $published      Spieler veröffentlicht (optional) true/false (Standard: true)
	 *
	 * @return boolean
	 */
	public static function checkMembership($playerRecord, $heute = false, $published = true)
	{
		if(!$published) return false; // Datensatz nicht veröffentlicht

		if($playerRecord->memberId > 89999) return false; // BdF-Mitglieder haben nur Nummern von 1 bis 89999
		
		if(!$heute) $heute = date('Ymd');

		$mitgliedschaften = StringUtil::deserialize($playerRecord->memberships); // String umwandeln
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
						$return = true;
					}
				}
				elseif($mitgliedschaft['from'] == 0 || $mitgliedschaft['from'] <= $heute)
				{
					// Beginndatum nicht gesetzt oder kleiner/gleich aktuellem Tag, also möglicherweise Mitglied
					if($mitgliedschaft['to'] == 0 || $mitgliedschaft['to'] > $heute)
					{
						// Endedatum nicht gesetzt oder größer aktuellem Tag, also Mitglied
						//echo 'OK '.$heute.'<br>';
						$return = true;
					}
				}
			}
		}

		// Verstorben- und Streichung-Prüfung unter Mitgliedschaftsprüfung gesetzt, damit
		// der Status geloggt werden kann
		// ==============================================================================
		// Verstorben prüfen
		if($playerRecord->death)
		{
			if($return)
			{
				// Mitgliedschaftszeitraum noch aktiv, aber verstorben = Fehler
				System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Spieler '.$playerRecord->nachname.','.$playerRecord->vorname.' (ID '.$playerRecord->id.') ist tot, hat aber eine aktive Mitgliedschaft.');
			}
			$return = false; // Spieler ist tot
		}

		// Streichung prüfen
		if($playerRecord->isDeletion && $playerRecord->streichung <= $heute)
		{
			if($return)
			{
				// Mitgliedschaftszeitraum noch aktiv, aber gestrichen = Fehler
				System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Spieler '.$playerRecord->nachname.','.$playerRecord->vorname.' (ID '.$playerRecord->id.') ist gestrichen, hat aber eine aktive Mitgliedschaft.');
			}
			$return = false;
		}

		return $return;
	}

	/**
	 * Berechnet das Alter eines Spielers zu einem Stichtag.
	 *
	 * @param int|string $birthday Geburtsdatum im Format JJJJMMTT
	 * @param int|string $datum    Stichtag im Format JJJJMMTT; ohne Angabe der heutige Tag
	 *
	 * @return int|string Das Alter in Jahren; 0, wenn kein Geburtsdatum vorliegt
	 *                     oder sich die Angaben nicht als Datum lesen lassen
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
		catch(\Exception $e)
		{
			return 0;
		}

	}

	/**
	 * Sucht in den Mitgliedschaften nach einem bestimmten Enddatum.
	 *
	 * @param string $value Serialisiertes Feld mit den Mitgliedschaften
	 * @param int    $datum Gesuchtes Mitgliedsende im Format JJJJMMTT
	 *
	 * @return bool True, wenn eine Mitgliedschaft mit diesem Ende vorliegt
	 */
	public static function searchMembership($value, $datum)
	{
		$heute = date('Ymd');
		$mitgliedschaften = StringUtil::deserialize($value); // String umwandeln
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
	 * @param string $value Serialisiertes Feld mit den Mitgliedschaften
	 * @param int    $jahr  Jahr des gesuchten Mitgliedschaftsbeginns
	 *
	 * @return boolean   true = Mitgliedschaftsbeginn gefunden / false = kein Mitgliedschaftsbeginn gefunden
	 */
	public static function isMemberBegin($value, $jahr)
	{
		$mitgliedschaften = StringUtil::deserialize($value); // String umwandeln

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
	 * Funktion KeineNenngeldzahlung
	 * =============================
	 * Sucht nach Nenngeldzahlungen in den letzten Monaten
	 *
	 * @param integer $spieler   ID des Spielers
	 * @param integer $monate    Anzahl der Monate
	 *
	 * @return boolean   true = Zahlungen vorhanden / false = keine Zahlungen gefunden
	 */
	public static function KeineNenngeldzahlung($spieler, $monate)
	{
		$startdatum = strtotime('-'.$monate.' months');

		$objBuchungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld WHERE pid = ? AND typ = ? AND datum >= ? AND published = ?")
		                                        ->execute($spieler, 'h', $startdatum, 1);
		if($objBuchungen->numRows == 0)
		{
			// Keine Nenngeldzahlungen gefunden im Zeitraum
			return true;
		}
		return false;
	}

	/**
	 * Funktion searchNoMembership
	 * ===========================
	 * Sucht in den Mitgliedschaften nach der letzten Mitgliedschaft
	 *
	 * @param string $value Serialisiertes Feld mit den Mitgliedschaften
	 * @param int    $datum Gesuchtes Mitgliedsende im Format JJJJMMTT
	 *
	 * @return boolean   true = Letzte Mitgliedschaft endet am Datum / false = Nach dem Datum gibt es noch Mitgliedschaften
	 */
	public static function searchNoMembership($value, $datum)
	{
		$heute = date('Ymd');
		$from = 0; // Von-Datum speichern
		$to = 0; // Bis-Datum speichern
		$datum_gefunden = false; // Speichert true, wenn das gesuchte $datum gefunden wurde

		$mitgliedschaften = StringUtil::deserialize($value); // String umwandeln
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
		$mitgliedschaften = StringUtil::deserialize($value); // String umwandeln
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
	 * function getBeitragssaldo
	 * =================================================================
	 * Liefert den aktuellen Stand des Beitragskontos. Dabei gilt folgende Regelung:
	 * - aktueller Monat ist Januar: Stand vom 31.12. des Vorjahres zurückgeben
	 * - aktueller Monat ist nicht Januar: Stand aktuell zurückgeben
	 *
	 * @param integer $id         ID des Spielers
	 *
	 * @return float              Saldo
	 */
	public static function getBeitragssaldo($id)
	{
		$datum = date('d.m.Y');
		// Umwandeln auf Mitternacht
		$tag = substr($datum, 0, 2);
		$monat = substr($datum, 3, 2);
		$jahr = substr($datum, 6, 4);

		if($monat == '01')
		{
			// Monat Januar ist aktuell, dann Saldodatum auf 31.12.JJJJ 23:59:59 setzen
			$datum_zeit = mktime(23, 59, 59, 12, 31, ((int) $jahr - 1));
		}
		else
		{
			// Monat Januar trifft nicht, dann Saldodatum auf aktuelles Datum 23:59:59 setzen
			$datum_zeit = mktime(23, 59, 59, $monat, $tag, $jahr);
		}

		// Buchungen des Spielers laden
		$objBuchungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_beitrag WHERE pid=? AND published=? ORDER BY datum ASC, sortierung ASC")
		                                        ->execute($id, 1);

		$saldo = 0;
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				// Nur Buchungen verwenden, die jünger oder gleich dem gewünschten Datum sind
				if($objBuchungen->datum <= $datum_zeit)
				{
					if($objBuchungen->saldoReset || $objBuchungen->resetRecord)
					{
						$saldo = 0; // Saldo soll hier resettet werden
					}
					switch($objBuchungen->typ)
					{
						case 'h':
							$saldo = bcadd($saldo, $objBuchungen->betrag, 2);
							break;
						case 's':
							$saldo = bcsub($saldo, $objBuchungen->betrag, 2);
							break;
						default:
					}
				}
			}
		}

		return $saldo;

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
	public static function getSaldo($pid, $konto = '', $datum = false, $sitzung = true)
	{
		$salden = array();
		if($sitzung) $session = Scope::getBackendSession(); // Sitzung laden
		$sql = ''; // SQL-String Filter und Suche initialisieren

		// konto-Variable prüfen und ggfs. korrigieren
		if($konto == 'nenngeld') $konto = '_nenngeld';
		if($konto == 'beitrag') $konto = '_beitrag';

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
		$objBuchungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$konto." WHERE pid=? AND published=?".$sql.' ORDER BY datum ASC, sortierung ASC')
		                                        ->execute($pid, 1);

		// Datum umwandeln
		if($datum)
		{
			// Datum wurde übergeben
			$datum_neu = $datum;
		}
		else
		{
			// Datum nicht übergeben im Formular
			$datum_neu = date('d.m.Y');
		}
		// Umwandeln auf Mitternacht
		$tag = substr($datum_neu, 0, 2);
		$monat = substr($datum_neu, 3, 2);
		$jahr = substr($datum_neu, 6, 4);
		$datum_neu2 = mktime(23, 59, 59, $monat, $tag, $jahr);

		// Buchungen auswerten
		$saldo = 0;
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				// Nur Buchungen verwenden, die jünger oder gleich dem gewünschten Datum sind
				if($objBuchungen->datum <= $datum_neu2)
				{
					if($objBuchungen->saldoReset || $objBuchungen->resetRecord)
					{
						$saldo = 0; // Saldo soll hier resettet werden
					}
					switch($objBuchungen->typ)
					{
						case 'h':
							//$saldo += $objBuchungen->betrag;
							$saldo = bcadd($saldo, $objBuchungen->betrag, 2);
							break;
						case 's':
							//$saldo -= $objBuchungen->betrag;
							$saldo = bcsub($saldo, $objBuchungen->betrag, 2);
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
	 * Setzt die Uhrzeit eines Datums auf 0:00 Uhr.
	 *
	 * Contao speichert Datumsfelder als Zeitstempel. Ohne diese Umwandlung
	 * enthielte ein am Nachmittag gespeichertes Datum auch die Uhrzeit, und
	 * Vergleiche auf Tagesgrenzen gingen schief.
	 *
	 * @param int|string $value Der gespeicherte Zeitstempel; 0 oder leer bleibt unverändert
	 *
	 * @return int|string Der Zeitstempel zur Mitternacht desselben Tages
	 */
	public function loadDate($value)
	{
		if($value) return strtotime(date('Y-m-d', $value) . ' 00:00:00');
		else return '';
	}

	/**
	 * Funktion updateResetbuchungen
	 * ============================
	 * Überprüft tl_fernschach_spieler_konto auf die Gültigkeit der globalen Resetbuchung
	 * Überprüft tl_fernschach_spieler_konto_nenngeld auf die Gültigkeit der globalen Resetbuchung
	 * Überprüft tl_fernschach_spieler_konto_beitrag auf die Gültigkeit der globalen Resetbuchung
	 */
	public function updateResetbuchungen(DataContainer $dc)
	{
		$update = (int)Config::get('fernschach_resetUpdate') + Config::get('fernschach_resetUpdate_time'); // Letztes Updatedatum + eingestellter Rhythmus

		// Aktualisierung notwendig
		if($update < time())
		{
			// Buchungen prüfen
			if(Config::get('fernschach_resetActive'))
			{
				$resetRecords = StringUtil::deserialize(Config::get('fernschach_resetRecords'), true); // Reset-Datensätze einlesen

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
				$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE resetRecord != ?")
				                                       ->execute('');
				$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE resetRecord != ?")
				                                       ->execute('');
				$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto_beitrag WHERE resetRecord != ?")
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
		$objBuchungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$suffix." ORDER BY pid ASC, datum ASC, sortierung ASC")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			$pid = 0; // Letzte Spieler-ID merken

			// Vorbelegung für den ersten Durchlauf: Zurückgesetzt werden die
			// drei Werte erst beim Wechsel auf einen neuen Spieler, beim ersten
			// Datensatz hat dieser Wechsel aber noch nicht stattgefunden.
			$resetDatensaetze = 0; // Bisher gefundene Reset-Datensätze
			$juengereBuchungen = false; // Jüngere Buchungen vorhanden
			$aeltereBuchungen = false; // Ältere Buchungen vorhanden

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
						$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto".$suffix." WHERE id = ?")
						                                       ->execute($objBuchungen->id);
						Scope::createVersion('tl_fernschach_spieler_konto'.$suffix, $objBuchungen->id);
					}
					elseif($resetDatensaetze > 1)
					{
						// Überflüssigen Reset-Datensatz löschen
						$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto".$suffix." WHERE id = ?")
						                                       ->execute($objBuchungen->id);
						Scope::createVersion('tl_fernschach_spieler_konto'.$suffix, $objBuchungen->id);
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
						$objInsert = Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto".$suffix." %s")
						                                     ->set($set)
						                                     ->execute();
						$resetDatensaetze++;
					}
				}
			}
		}
	}

	/**
	 * Funktion checkResetbuchungen
	 * ============================
	 * Sucht in den Buchungen eines Spielers nach globalen Reset-Buchungen, prüft und aktualisiert diese
	 *
	 * @param int|string $playerId ID des Spielers
	 *
	 * @return void Es wird direkt in die Datenbank geschrieben
	 */
	public static function checkResetbuchungen($playerId)
	{
		$BuchungenJuenger = false; // Boolean, um festzustellen das es jüngere Buchungen als Reset gibt
		$BuchungenAelter = false; // Boolean, um festzustellen das es ältere Buchungen als Reset gibt
		$resetDatensaetze = 0; // Zähler, um festzustellen wieviel Reset-Datensätze existieren. Erlaubt ist max. 1

		// Reset-Datensatz-Werte setzen
		if(Config::get('fernschach_resetActive'))
		{
			$typGlobal = Config::get('fernschach_resetSaldo') < 0 ? 's' : 'h';
			$betragGlobal = abs(Config::get('fernschach_resetSaldo'));
			$datumGlobal = abs(Config::get('fernschach_resetDate'));

			// Reset-Buchungen suchen. Bis Version 2.1.0 stand hier $id — eine
			// Variable, die es in dieser Methode gar nicht gibt. Die Abfrage lief
			// damit gegen pid = null und fand nie etwas.
			$objResets = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid = ? AND resetRecord = ?")
			                                     ->execute($playerId, 1);
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
						$objUpdate = Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
						                                     ->set($set)
						                                     ->execute($objResets->id);
						Scope::createVersion('tl_fernschach_spieler_konto', $objResets->id);
						$resetDatensaetze++;
					}
					elseif($resetDatensaetze > 1)
					{
						// Überflüssige Reset-Buchung löschen
						$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
						                                       ->execute($objResets->id);
						Scope::createVersion('tl_fernschach_spieler_konto', $objResets->id);
					}
				}
			}
		}

		// Alle Buchungen des Spielers vom ältesten bis zum jüngsten Datensatz sortiert einlesen
		$objBuchungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid = ? ORDER BY datum ASC, sortierung ASC")
		                                        ->execute($playerId);
		if($objBuchungen->numRows)
		{
			$resetDatensaetze = 0;
			while($objBuchungen->next())
			{
				if($objBuchungen->resetRecord && !$BuchungenJuenger && !$BuchungenAelter)
				{
					// Reset-Datensatz hier unnötig, da es keine jüngeren oder älteren Buchungen gibt -> also löschen
					$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
					                                       ->execute($objBuchungen->id);
					Scope::createVersion('tl_fernschach_spieler', $objBuchungen->id);
				}
				elseif($objBuchungen->resetRecord && $BuchungenJuenger && $BuchungenAelter)
				{
					$resetDatensaetze++;
					// Reset-Datensatz gefunden, und es gibt jüngeren oder älteren Buchungen -> also aktualisieren/löschen
					if(Config::get('fernschach_resetActive'))
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
							$objUpdate = Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
							                                     ->set($set)
							                                     ->execute($objBuchungen->id);
							Scope::createVersion('tl_fernschach_spieler_konto', $objBuchungen->id);
						}
					}
					else
					{
						// Reset-Datensatz löschen, da unerwünscht
						$objLoeschen = Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
						                                       ->execute($objBuchungen->id);
						Scope::createVersion('tl_fernschach_spieler_konto', $objBuchungen->id);
					}
				}
				elseif(!$objBuchungen->resetRecord && $BuchungenJuenger && $BuchungenAelter && !$resetDatensaetze && Config::get('fernschach_resetActive'))
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
					$objInsert = Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto %s")
					                                     ->set($set)
					                                     ->execute();
					$resetDatensaetze++;
				}
				elseif(!$objBuchungen->resetRecord)
				{
					// Normaler Datensatz, Buchungsdatum vergleichen mit Resetdatum
					if(Config::get('fernschach_resetActive'))
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
	 *
	 * @param bool $id
	 * @param bool $feld
	 *
	 * @return array
	 */
	public static function getSpieler($id = false, $feld = false)
	{
		static $spieler;

		// Spielerdaten laden, wenn noch nicht geschehen. Das Semikolon hinter der
		// Bedingung stand hier jahrelang und hat die Abfrage wirkungslos gemacht —
		// die gesamte Spielertabelle wurde bei jedem Aufruf neu gelesen.
		if (!$spieler)
		{
			$objSpieler = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler ORDER BY nachname ASC, vorname ASC")
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
	 *
	 * @param bool $id
	 * @param bool $member
	 *
	 * @return object
	 */
	public static function getSpielerdatensatz($id = false, $member = false)
	{
		if($id)
		{
			// Suche anhand ID
			$objSpieler = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
			                                      ->execute($id);
			return $objSpieler;
		}

		if($member)
		{
			// Suche anhand Mitgliedsnummer
			$objSpieler = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE memberId = ?")
			                                      ->limit(1)
			                                      ->execute($member);
			return $objSpieler;
		}

		return false;
	}

	/**
	 * Lädt zu jeder Meldung den Namen des gemeldeten Spielers.
	 *
	 * Gebraucht wird das in der Teilnehmerliste eines Turniers, die nur die
	 * Meldungs-ID kennt. Das Ergebnis wird für die Dauer der Anfrage gemerkt,
	 * damit die Liste nicht für jede Zeile erneut abfragt.
	 *
	 * Bis Version 2.1.0 war die Methode nicht als static deklariert, wurde aber
	 * statisch aufgerufen — unter PHP 8 ein Fatal Error. Außerdem stand hinter
	 * der if-Bedingung ein Semikolon, wodurch der Zwischenspeicher nie griff.
	 *
	 * @return array Meldungs-ID => "Vorname Nachname"; leer, wenn es keine
	 *               Meldungen gibt
	 */
	public static function getMeldungen()
	{
		static $spieler;

		if(!$spieler)
		{
			$objSpieler = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen ORDER BY nachname ASC, vorname ASC")
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
	 *
	 * @param mixed $id
	 *
	 * @return object
	 */
	public static function getTurnierdatensatz($id)
	{
		if($id)
		{
			// Suche anhand ID
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($id);
			return $objTurnier;
		}
		return false;
	}

	/**
	 * Beschreibt, ob ein Spieler ein Konto für den internen Bereich hat.
	 *
	 * Die Verbindung läuft über tl_member.fernschach_memberId, die auf die ID
	 * des Spielers zeigt; angelegt und gepflegt wird sie vom Cronjob
	 * Mitgliederprüfung. Ein Spieler ohne Frontend-Konto kann sich nicht
	 * anmelden und damit weder Turniermeldungen abgeben noch seinen Kontoauszug
	 * einsehen — für die Geschäftsstelle ist das eine der häufigsten Rückfragen,
	 * deshalb steht es im Excel-Export.
	 *
	 * @param int|string $spieler ID des Spielers aus tl_fernschach_spieler
	 *
	 * @return string 'Ja' bei einem nutzbaren Konto, 'Ja (gesperrt)' bei einem
	 *                deaktivierten und 'Nein', wenn es gar keines gibt. Gibt es
	 *                mehrere Konten, zählt das erste gefundene aktive
	 */
	public static function getInternerBereich($spieler)
	{
		if(!$spieler)
		{
			return 'Nein';
		}

		$objMember = Database::getInstance()->prepare("SELECT disable FROM tl_member WHERE fernschach_memberId = ? ORDER BY disable ASC")
		                                     ->execute($spieler);

		if(!$objMember->numRows)
		{
			return 'Nein';
		}

		return $objMember->disable ? 'Ja (gesperrt)' : 'Ja';
	}

	/**
	 * Sucht Spieler für die Autovervollständigung im Mannschaftsmeldeformular.
	 *
	 * Gesucht wird in Nachname, Vorname, BdF-Mitgliedsnummer und ICCF-ID. Ein
	 * Suchbegriff aus mehreren Wörtern („muster max") wird dabei so behandelt,
	 * dass jedes Wort irgendwo im Datensatz vorkommen muss — sonst fände die
	 * Eingabe „Mustermann Max" nichts, weil in der Datenbank Vor- und Nachname
	 * getrennt stehen.
	 *
	 * Angeboten werden nur veröffentlichte, nicht archivierte BdF-Mitglieder,
	 * die auch tatsächlich gemeldet werden dürfen: entweder mit SEPA-Mandat für
	 * den Beitrag oder mit ausgeglichenem Beitragskonto.
	 *
	 * @param string $suche      Der eingetippte Text
	 * @param int    $hoechstzahl Wie viele Treffer höchstens zurückkommen
	 *
	 * @return array Liste mit den Schlüsseln 'id', 'text' und 'info'; leer, wenn
	 *               nichts passt
	 */
	public static function sucheSpieler($suche, $hoechstzahl = 15)
	{
		$suche = trim((string) $suche);

		if ('' === $suche)
		{
			return array();
		}

		// Suchbedingung je Wort zusammenbauen
		$bedingungen = array();
		$parameter = array();

		foreach (preg_split('/\s+/', $suche) as $wort)
		{
			if ('' === $wort)
			{
				continue;
			}

			$bedingungen[] = '(nachname LIKE ? OR vorname LIKE ? OR memberId LIKE ? OR memberInternationalId LIKE ?)';
			$muster = '%'.$wort.'%';
			$parameter[] = $muster;
			$parameter[] = $muster;
			$parameter[] = $muster;
			$parameter[] = $muster;
		}

		if (!$bedingungen)
		{
			return array();
		}

		$objSpieler = Database::getInstance()->prepare(
			'SELECT id, nachname, vorname, memberId, memberInternationalId, sepaBeitrag, memberships, death, isDeletion, streichung, published'
			.' FROM tl_fernschach_spieler'
			.' WHERE published = ? AND archived = ? AND '.implode(' AND ', $bedingungen)
			.' ORDER BY nachname ASC, vorname ASC'
		)->execute('1', '', ...$parameter);

		$treffer = array();

		while ($objSpieler->next())
		{
			// Nur wirkliche Mitglieder anbieten
			if (!self::checkMembership($objSpieler))
			{
				continue;
			}

			// Ohne SEPA-Mandat nur, wenn das Beitragskonto ausgeglichen ist
			if (!$objSpieler->sepaBeitrag && self::getBeitragssaldo($objSpieler->id) < 0)
			{
				continue;
			}

			$treffer[] = array
			(
				'id'   => (int) $objSpieler->id,
				'text' => $objSpieler->nachname.', '.$objSpieler->vorname,
				'info' => 'BdF-Nr. '.$objSpieler->memberId.($objSpieler->memberInternationalId ? ' / ICCF-ID '.$objSpieler->memberInternationalId : ''),
			);

			if (count($treffer) >= $hoechstzahl)
			{
				break;
			}
		}

		return $treffer;
	}

	/**
	 * Zählt, wie oft ein Spieler für ein Turnier bereits gemeldet ist.
	 *
	 * Gezählt werden alle vorhandenen Datensätze, unabhängig vom Bearbeitungs-
	 * stand: Eine Bewerbung, die noch niemand zu- oder abgesagt hat, ist genauso
	 * eine Meldung wie eine bereits bestätigte.
	 *
	 * @param int|string $turnier      ID des Turniers (pid der Meldung)
	 * @param int|string $spieler      ID des Spielers aus tl_fernschach_spieler
	 * @param bool       $blnBewerbung true zählt in den Bewerbungen, false in den
	 *                                 Anmeldungen
	 *
	 * @return int Anzahl der vorhandenen Meldungen; 0, wenn eine der beiden IDs
	 *             fehlt
	 */
	public static function zaehleMeldungen($turnier, $spieler, $blnBewerbung = false)
	{
		if(!$turnier || !$spieler)
		{
			return 0;
		}

		$strTabelle = $blnBewerbung ? 'tl_fernschach_turniere_bewerbungen' : 'tl_fernschach_turniere_meldungen';

		$objMeldungen = Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM ".$strTabelle." WHERE pid = ? AND spielerId = ?")
		                                        ->execute($turnier, $spieler);

		return (int) $objMeldungen->anzahl;
	}

	/**
	 * Prüft, ob sich ein Spieler für ein Turnier (noch) melden darf.
	 *
	 * Wie oft das erlaubt ist, steht am Turnier im Feld maxMeldungen; 0 bedeutet
	 * unbegrenzt. Der Grund für die Begrenzung: Ohne sie haben sich Mitglieder
	 * mehrfach für dasselbe Turnier beworben, weil sie die Bestätigungsmail
	 * nicht gesehen haben — im Extremfall neunmal für dasselbe Turnier.
	 *
	 * @param object     $objTurnier   Turnierdatensatz, mindestens mit den
	 *                                 Feldern id und maxMeldungen
	 * @param int|string $spieler      ID des Spielers aus tl_fernschach_spieler
	 * @param bool       $blnBewerbung true prüft die Bewerbungen, false die
	 *                                 Anmeldungen
	 *
	 * @return bool True, wenn eine weitere Meldung erlaubt ist. False, sobald
	 *              die eingestellte Zahl erreicht ist. Ohne Turnier oder ohne
	 *              Spieler ebenfalls false, weil dann gar nichts zuzuordnen wäre
	 */
	public static function meldungErlaubt($objTurnier, $spieler, $blnBewerbung = false)
	{
		if(!$objTurnier || !$spieler)
		{
			return false;
		}

		$intMax = (int) ($objTurnier->maxMeldungen ?? 0);

		// 0 = unbegrenzt
		if($intMax < 1)
		{
			return true;
		}

		return self::zaehleMeldungen($objTurnier->id, $spieler, $blnBewerbung) < $intMax;
	}

	/**
	 * Sucht für eine Spieler-ID alle Anmeldungen und Bewerbungen und gibt diese absteigend sortiert nach Meldedatum zurück
	 *
	 * @param mixed $id
	 *
	 * @return object
	 */
	public static function getAnmeldungenBewerbungen($id)
	{
		// Der Backend-Einstieg heißt seit Contao 4 "contao" und wird über den
		// Router ermittelt. Die frühere Fallunterscheidung über die Konstante
		// VERSION ist entfallen — es gibt sie in Contao 5 nicht mehr.
		$linkprefix = System::getContainer()->get('router')->generate('contao_backend');
		$imageEdit = Image::getHtml('edit.svg', 'Bewerbung des Mitglieds bearbeiten');

		$objAnmeldungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE spielerId = ?")
		                                          ->execute($id);
		$objBewerbungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_bewerbungen WHERE spielerId = ?")
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
					'link'       => '<a href="'.$linkprefix.'?do=fernschach-turniere&amp;table=tl_fernschach_turniere_meldungen&amp;act=edit&amp;id='.$objAnmeldungen->id.'&amp;popup=1&amp;rt='.Scope::getRequestToken().'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>'
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
					'link'       => '<a href="'.$linkprefix.'?do=fernschach-turniere&amp;table=tl_fernschach_turniere_bewerbungen&amp;act=edit&amp;id='.$objBewerbungen->id.'&amp;popup=1&amp;rt='.Scope::getRequestToken().'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>'
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

	/**
	 * Funktion getPreview
	 * ===================================================================
	 * Erstellt eine E-Mail-Vorschau
	 *
	 * @param mixed $template
	 * @param mixed $content
	 * @param mixed $signatur
	 * @param mixed $Spieler
	 *
	 * @return string
	 */
	public static function getPreview($template, $content, $signatur, $Spieler)
	{
		// Tokens der Art ##name## zuweisen
		$arrTokens = array
		(
			'content'              => $content,
			'signatur'             => $signatur,
			'spieler_nachname'     => $Spieler->nachname,
			'spieler_vorname'      => $Spieler->vorname,
			'spieler_titel'        => $Spieler->titel,
			'spieler_anrede'       => $Spieler->anrede,
			'spieler_briefanrede'  => $Spieler->briefanrede,
			'spieler_geschlecht'   => $Spieler->sex,
			'spieler_geburtstag'   => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($Spieler->birthday),
			'spieler_geburtsort'   => $Spieler->birthplace,
			'spieler_verstorben'   => $Spieler->death,
			'spieler_sterbetag'    => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($Spieler->deathday),
			'spieler_sterbeort'    => $Spieler->deathplace,
		);

		$ausgabe = '';

		if($template)
		{
			// Template auswerten
			$ausgabe = Tokens::replace($template, $arrTokens);
		}
		else
		{
			// Ohne Template
			$ausgabe = Tokens::replace($content.$signatur, $arrTokens);
		}

		$ausgabe = StringUtil::restoreBasicEntities($ausgabe); // [nbsp] und Co. ersetzen
		//$ausgabe = nl2br($ausgabe);
		return $ausgabe;
	}

}
