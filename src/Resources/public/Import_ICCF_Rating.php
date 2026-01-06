<?php
ini_set('display_errors', '1');
set_time_limit(0);

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
use Contao\Controller;

/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
define('TL_SCRIPT', 'bundles/contaofernschach/Import_ICCF_Rating.php');
require($_SERVER['DOCUMENT_ROOT'].'/../system/initialize.php');

/**
 * Class LinkSearch
 *
 */
class Import_ICCF_Rating
{
	var $sitzung = array();
	var $logeintrag = '';

	public function __construct()
	{
		// Sitzung laden
		$session = \System::getContainer()->get('session');
		$this->sitzung = $session->get('iccf_import');
	}

	public function run()
	{

		$importdatei = $this->sitzung['pfad'].'/'.$this->sitzung['datei'];
		$zeilen_array = file($importdatei);

		$count = 0;
		$zeile = \Input::get('zeile');
		for($x = $zeile; $x < $this->sitzung['zeilen']; $x++)
		{
			//$this->logeintrag = '['.$x.'] '.trim($zeilen_array[$x]);
			self::importiereDatensatz(trim($zeilen_array[$x]));
			//log_message($this->logeintrag,'iccf-import.log');
			if($count == 500) break;
			$count++;
		}
		$zeile = $zeile + $count;

		$ausgabe = array
		(
			'titel'  => 'Import läuft',
			'gesamt' => $this->sitzung['zeilen'],
			'zeile'  => ($zeile+1),
		);

		echo json_encode($ausgabe);
	}

	public function importiereDatensatz($zeile)
	{
		$spalte = explode(';', $zeile);
		$spielername = explode(',', $spalte[3]);
		$nachname = trim($spielername[0]);
		$vorname = isset($spielername[1]) ? trim($spielername[1]) : '';

		// Nach Spieler mit ICCF-ID (aus Spalte 1) suchen
		$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_iccf_players WHERE iccfid = ?")
		                                     ->execute($spalte[0]);
		if($objPlayer->numRows)
		{
			// ICCF-ID gefunden
			$playerId = $objPlayer->id;
			// Prüfen ob das Listendatum aktueller ist als das Aktualisierungsdatum des Spielers
			if($this->sitzung['listDate'] > $objPlayer->tstamp)
			{
				// Listendatum ist aktueller, jetzt prüfen ob es Veränderungen beim Spieler gibt
				$change = '';
				if($objPlayer->country != $spalte[1]) $change .= '['.date('d.m.Y H:i').'] '.$objPlayer->country.' &#10132; '.$spalte[1]."\n";
				if($objPlayer->surname != $nachname) $change .= '['.date('d.m.Y H:i').'] '.$objPlayer->surname.' &#10132; '.$nachname."\n";;
				if($objPlayer->prename != $vorname) $change .= '['.date('d.m.Y H:i').'] '.$objPlayer->prename.' &#10132; '.$vorname."\n";;
				if($change)
				{
					// Spieler aktualisieren
					$set_player = array
					(
						'tstamp'      => time(),
						'country'     => $spalte[1], // Land in Spalte 2
						'surname'     => $nachname, // Nachname
						'prename'     => $vorname, // Vorname
						'intern'      => $objPlayer->intern.$change,
					);
					// Versionsmanager initialisieren
					//$versions = new \Versions('tl_fernschach_iccf_players', $objPlayer->id);
					//$versions->initialize();
					$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_iccf_players %s WHERE id = ?")
					                                     ->set($set_player)
					                                     ->execute($objPlayer->id);
					// Neue Version anlegen
					//$versions->create();
				}
			}
		}
		else
		{
			// ICCF-ID nicht gefunden, Spieler neu eintragen
			$set_player = array
			(
				'tstamp'      => time(),
				'iccfid'      => $spalte[0], // ICCF-ID in Spalte 1
				'country'     => $spalte[1], // Land in Spalte 2
				'surname'     => $nachname, // Nachname
				'prename'     => $vorname, // Vorname
				'intern'      => NULL,
				'published'   => true
			);
			$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_iccf_players %s")
			                                     ->set($set_player)
			                                     ->execute();
			$playerId = $objInsert->insertId;
		}

		// ======================================================
		// Wertungszahl eintragen/aktualisieren
		// ======================================================

		// Nach der Wertungszahl suchen
		$objRating = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_iccf_ratings WHERE listId = ? AND pid = ?")
		                                     ->execute($this->sitzung['listId'], $playerId);
		if($objRating->numRows)
		{
			// Mindestens ein Datensatz gefunden, jetzt alle aktualisieren
			while($objRating->next())
			{
				$set_rating = array
				(
					'tstamp'          => time(),
					'pid'             => $playerId, // ID des Spielers aus tl_fernschach_iccf_players
					'listId'          => $this->sitzung['listId'], // ID der Ratingliste aus tl_fernschach_iccf_ratinglists
					'ratingDate'      => $this->sitzung['listDate'], // Beginn-Datum der Ratingliste
					'title'           => $spalte[2], // Titel in Spalte 3
					'ratingDeviation' => $spalte[6], // Titel in Spalte 7
					'flag'            => $spalte[7], // Titel in Spalte 8
					'rating'          => $spalte[5], // Titel in Spalte 6
					'games'           => $spalte[4], // Titel in Spalte 5
					'published'       => true
				);
				//// Versionsmanager initialisieren
				//$versions = new \Versions('tl_fernschach_iccf_ratings', $objRating->id);
				//$versions->initialize();
				$objUpdate = \Database::getInstance()->prepare("UPDATE tl_fernschach_iccf_ratings %s WHERE id = ?")
				                                     ->set($set_rating)
				                                     ->execute($objRating->id);
				// Neue Version anlegen
				//$versions->create();
			}
		}
		else
		{
			// Kein Datensatz gefunden, also neu anlegen
			$set_rating = array
			(
				'tstamp'          => time(),
				'pid'             => $playerId, // ID des Spielers aus tl_fernschach_iccf_players
				'listId'          => $this->sitzung['listId'], // ID der Ratingliste aus tl_fernschach_iccf_ratinglists
				'ratingDate'      => $this->sitzung['listDate'], // Beginn-Datum der Ratingliste
				'title'           => $spalte[2], // Titel in Spalte 3
				'ratingDeviation' => $spalte[6], // Titel in Spalte 7
				'flag'            => $spalte[7], // Titel in Spalte 8
				'rating'          => $spalte[5], // Titel in Spalte 6
				'games'           => $spalte[4], // Titel in Spalte 5
				'published'       => true
			);
			$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_iccf_ratings %s")
			                                     ->set($set_rating)
			                                     ->execute();
		}
	}
}

/**
 * Instantiate controller
 */
$objClick = new Import_ICCF_Rating();
$objClick->run();
