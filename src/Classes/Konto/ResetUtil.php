<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Konto;

class ResetUtil extends \Backend
{

	var $Resets = array();

	public function __construct()
	{
		self::getResets();
	}

	/**
	 * Funktion Pruefung
	 * ===========================================================================================
	 * Überprüft ein Buchungskonto auf korrekt gesetzte globale Resetbuchungen
	 *
	 * @param string $konto          h, b oder n
	 * @param integer $id            ID des Spielers (pid im Konto)
	 *
	 * @return -
	 */
	public function Pruefung($konto, $id)
	{
		// Kontosuffix ermitteln, um auf die richtige Tabelle zuzugreifen
		switch($konto)
		{
			case 'h': $suffix = ''; break;
			case 'b': $suffix = '_beitrag'; break;
			case 'n': $suffix = '_nenngeld'; break;
			default: $suffix = '';
		}

		// ======================================================
		// Jüngste und älteste Buchung feststellen.
		// Globale Restbuchungen werden nicht berücksichtigt.
		// ======================================================
		$BuchungAlt = array('id'=>0, 'datum'=>0); // Speichert Datum und ID der ältesten Buchung
		$BuchungJung = array('id'=>0, 'datum'=>0); // Speichert Datum und ID der jüngsten Buchung

		// Alle Buchungen des Spielers aufsteigend nach Datum laden
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$suffix." WHERE pid = ? AND resetRecord = ? ORDER BY datum ASC, sortierung ASC")
		                                        ->execute($id, '');
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				if(!$BuchungAlt['id'])
				{
					// Noch keine älteste Buchung gespeichert, jetzt speichern
					$BuchungAlt = array
					(
						'id'    => $objBuchungen->id,
						'datum' => $objBuchungen->datum
					);
				}
				// Jüngste Buchung speichern
				$BuchungJung = array
				(
					'id'    => $objBuchungen->id,
					'datum' => $objBuchungen->datum
				);
			}
		}
		//echo '<b>Kontozeitraum (ohne Resetbuchungen)</b>: ';
		//print_r($BuchungJung);
		//echo '<br>';
		//print_r($BuchungAlt);
		//echo '<br>';

		// ======================================================
		// Alle globalen Resetbuchungen des Spielers laden
		// ======================================================
		$Resetbuchungen = array();
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$suffix." WHERE pid = ? AND resetRecord != ?")
		                                        ->execute($id, '');
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				$Resetbuchungen[] = array
				(
					'konto'     => $konto,
					'nummer'    => $objBuchungen->resetRecord,
					'datum'     => $objBuchungen->datum,
					'betrag'    => $objBuchungen->betrag,
					'typ'       => $objBuchungen->typ,
					'id'        => $objBuchungen->id,
					'correctly' => false // Es erfolgt nachfolgend eine Prüfung. Später werden alle false-Buchungen gelöscht
				);
			}
		}

		// ======================================================
		// Resetbuchungen prüfen, ob diese definiert sind
		// ======================================================
		for($x = 0; $x < count($Resetbuchungen); $x++)
		{
			for($y = 0; $y < count($this->resets); $y++)
			{
				if($this->resets[$y]['konto'] == $Resetbuchungen[$x]['konto'] && $this->resets[$y]['nummer'] == $Resetbuchungen[$x]['nummer'] && $this->resets[$y]['datum'] == $Resetbuchungen[$x]['datum'] && $this->resets[$y]['betrag'] == $Resetbuchungen[$x]['betrag'] && $this->resets[$y]['typ'] == $Resetbuchungen[$x]['typ'])
				{
					$Resetbuchungen[$x]['correctly'] = true; // Buchung ist korrekt
					$this->resets[$y]['found'] = true; // Definierte Buchung als gespeichert markieren

				//echo '<b>Definiert = Gespeichert</b>: ';
				//print_r($this->resets[$y]);
				//echo '<br>';
				//print_r($Resetbuchungen[$x]);
				//echo '<br>';
					break;
				}
			}
		}

		// ======================================================
		// Resetbuchungen prüfen, ob diese innerhalb des Kontozeitraums liegen
		// (jüngste und älteste Buchung)
		// ======================================================
		for($x = 0; $x < count($Resetbuchungen); $x++)
		{
			if($Resetbuchungen[$x]['datum'] > $BuchungJung['datum'] || $Resetbuchungen[$x]['datum'] < $BuchungAlt['datum'])
			{
				// Gespeicherte Buchung außerhalb des Kontozeitraums, deshalb als löschbar markieren
				$Resetbuchungen[$x]['correctly'] = false;
			}
		}

		// ======================================================
		// Fehlende definierte Resetbuchungen finden und in DB speichern
		// ======================================================
		//echo '<pre>';
		//print_r($this->resets);
		//echo '</pre>';
		for($y = 0; $y < count($this->resets); $y++)
		{
			if($this->resets[$y]['konto'] == $konto && !$this->resets[$y]['found'])
			{
				// Buchung gehört zum Konto, wurde aber nicht gefunden in Datenbank
				if($this->resets[$y]['datum'] < $BuchungJung['datum'] && $this->resets[$y]['datum'] > $BuchungAlt['datum'])
				{
					//echo '<pre>';
					//print_r($this->resets[$y]);
					//print_r($BuchungJung);
					//print_r($BuchungAlt);
					//echo '</pre>';
					// Buchung liegt im korrekten Kontozeitraum
					// Reset-Buchung anlegen
					$set = array
					(
						'pid'              => $id,
						'tstamp'           => time(),
						'resetRecord'      => $this->resets[$y]['nummer'],
						'betrag'           => $this->resets[$y]['betrag'],
						'datum'            => $this->resets[$y]['datum'],
						'saldoReset'       => 1,
						'typ'              => $this->resets[$y]['typ'],
						'verwendungszweck' => 'Saldo global neu gesetzt',
					);
					$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto".$suffix." %s")
					                                     ->set($set)
					                                     ->execute();
				}
				else
				{
					//echo '<b>Fehlende Buchung nicht im Zeitraum</b>: ';
					//print_r($this->resets[$y]);
					//echo '<br>';
				}
			}
		}

		// ======================================================
		// Ungültige Resetbuchungen löschen
		// ======================================================
		for($x = 0; $x < count($Resetbuchungen); $x++)
		{
			if(!$Resetbuchungen[$x]['correctly'])
			{
				// Löschbare Buchung gefunden
				$objLoeschen = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto".$suffix." WHERE id = ?")
				                                       ->execute($Resetbuchungen[$x]['id']);
				$version = new \Versions('tl_fernschach_spieler_konto'.$suffix, $Resetbuchungen[$x]['id']);
				$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
				$version->create();
			}
		}

	}

	/**
	 * Funktion getResets
	 * ===========================================================================================
	 * Liefert ein Array mit den global definierten Resetbuchungen sortiert aufsteigend nach Datum
	 *
	 * @param -
	 *
	 * @return array
	 * Beispiel:
	 * Array
	 * (
	 *     [0] => Array
	 *         (
	 *             [konto] => b
	 *             [datum] => 1577833200 (immer 0 Uhr des Tages)
	 *             [betrag] => 0
	 *             [typ] => h
	 *         )
	 *     [1] => Array
	 *         (
	 *             [konto] => h
	 *             [datum] => 1577833200 (immer 0 Uhr des Tages)
	 *             [betrag] => 0
	 *             [typ] => h
	 *         )
	 * )
	 */
	public function getResets()
	{
		$arr = array();

		// Reset-Datensätze einlesen, wenn eingeschaltet
		if(isset($GLOBALS['TL_CONFIG']['fernschach_resetActive']) && $GLOBALS['TL_CONFIG']['fernschach_resetActive'])
		{
			$resetRecords = (array)unserialize($GLOBALS['TL_CONFIG']['fernschach_resetRecords']); // Reset-Datensätze einlesen

			// Alle Reset-Datensätze auswerten
			foreach($resetRecords as $resetRecord)
			{
				$nummer = abs($resetRecord['nummer']);
				$typ = $resetRecord['saldo'] < 0 ? 's' : 'h';
				$betrag = abs($resetRecord['saldo']);
				$datum = abs($resetRecord['datum']);
				$konten = $resetRecord['konten'];
				if(count($konten))
				{
					foreach($konten as $konto)
					{
						$arr[] = array
						(
							'konto'  => $konto,
							'nummer' => $nummer,
							'datum'  => $datum,
							'betrag' => $betrag,
							'typ'    => $typ,
							'found'  => false, // Wird benötigt, um fehlende Buchungen zu übernehmen
						);
					}
				}
				// Nach Datum aufsteigend sortieren
				$arr = \Schachbulle\ContaoHelperBundle\Classes\Helper::sortArrayByFields($arr, array('datum'=>SORT_ASC));
			}
		}
		$this->resets = $arr;
	}

}
