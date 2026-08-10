<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Importiert eine ICCF-Wertungsliste häppchenweise.
 *
 * Eine Wertungsliste hat gut und gern 50.000 Zeilen; ein einzelner Aufruf würde
 * in jedes Zeitlimit laufen. Das Backend ruft diese Route deshalb per fetch()
 * immer wieder auf (siehe Resources/public/js/import_iccf.js) und bekommt jedes
 * Mal zurück, wie weit der Import gekommen ist.
 *
 * Bis Version 1.9.6 lag hier statt einer Route die direkt aufrufbare Datei
 * Resources/public/Import_ICCF_Rating.php, die sich über system/initialize.php
 * selbst einen halben Contao hochgezogen hat. Das gibt es in Contao 5 nicht mehr
 * — und öffentlich erreichbarer PHP-Code im Asset-Verzeichnis war ohnehin
 * nichts, was man sich wünscht.
 */
class IccfImportController
{
	/**
	 * Wie viele Zeilen ein Aufruf höchstens verarbeitet.
	 *
	 * Der Wert stammt aus der abgelösten Datei und hat sich in der Praxis
	 * bewährt: klein genug fürs Zeitlimit, groß genug, dass die Liste nicht
	 * hundertfach angefragt wird.
	 */
	private const ZEILEN_JE_AUFRUF = 500;

	private ContaoFramework $framework;

	/**
	 * @param ContaoFramework $framework Wird gebraucht, um das Contao-Framework zu
	 *                                   starten — ohne das gibt es keine
	 *                                   Datenbankverbindung über Contao\Database
	 */
	public function __construct(ContaoFramework $framework)
	{
		$this->framework = $framework;
	}

	/**
	 * Verarbeitet den nächsten Block der Wertungsliste.
	 *
	 * Die Angaben zur Datei stehen unter dem Schlüssel "iccf_import" in der
	 * Sitzung; dorthin geschrieben hat sie Classes\ImportRating beim Hochladen.
	 * Fehlen sie, wurde entweder nie eine Datei hochgeladen oder die Sitzung ist
	 * abgelaufen.
	 *
	 * @param Request $request Die laufende Anfrage; ausgewertet wird nur der
	 *                         Parameter "zeile" mit der zuletzt erreichten Zeile
	 *
	 * @return JsonResponse Feld mit den Schlüsseln "titel", "gesamt" und "zeile".
	 *                      "zeile" ist die als Nächstes zu verarbeitende Zeile;
	 *                      erreicht sie "gesamt", ist der Import fertig. Im
	 *                      Fehlerfall steht der Grund in "titel" und "gesamt"
	 *                      sowie "zeile" sind 0
	 */
	public function __invoke(Request $request): JsonResponse
	{
		$this->framework->initialize();

		$session = $request->hasSession() ? $request->getSession() : null;
		$daten = null !== $session ? $session->get('iccf_import') : null;

		if (!\is_array($daten) || !isset($daten['pfad'], $daten['datei'], $daten['zeilen']))
		{
			return new JsonResponse(array
			(
				'titel'  => 'Keine Importdaten in der Sitzung — bitte die Datei erneut hochladen',
				'gesamt' => 0,
				'zeile'  => 0,
			));
		}

		$importdatei = $daten['pfad'].'/'.$daten['datei'];

		if (!is_readable($importdatei))
		{
			return new JsonResponse(array
			(
				'titel'  => 'Die hochgeladene Datei ist nicht mehr lesbar',
				'gesamt' => 0,
				'zeile'  => 0,
			));
		}

		$zeilen = file($importdatei);
		$start = max(0, (int) $request->query->get('zeile', 0));
		$gesamt = (int) $daten['zeilen'];
		$anzahl = 0;

		for ($x = $start; $x < $gesamt && $anzahl < self::ZEILEN_JE_AUFRUF; ++$x)
		{
			if (isset($zeilen[$x]))
			{
				$this->importiereDatensatz(trim($zeilen[$x]), $daten);
			}

			++$anzahl;
		}

		return new JsonResponse(array
		(
			'titel'  => 'Import läuft',
			'gesamt' => $gesamt,
			'zeile'  => $start + $anzahl,
		));
	}

	/**
	 * Trägt eine einzelne Zeile der Wertungsliste in die Datenbank ein.
	 *
	 * Die Zeile ist semikolongetrennt und enthält der Reihe nach ICCF-ID, Land,
	 * Titel, Name ("Nachname, Vorname"), Partienzahl, Wertungszahl, Abweichung
	 * und Kennzeichen. Ein unbekannter Spieler wird angelegt, ein bekannter nur
	 * dann aktualisiert, wenn die Liste jünger ist als sein letzter Stand — und
	 * auch dann nur, wenn sich wirklich etwas geändert hat. Jede Änderung wird
	 * im internen Feld des Spielers protokolliert.
	 *
	 * @param string $zeile Eine Zeile der CSV-Datei ohne Zeilenumbruch
	 * @param array  $daten Die Angaben aus der Sitzung, gebraucht werden
	 *                      'listId' (Datensatz der Wertungsliste) und 'listDate'
	 *                      (Beginndatum der Liste als Zeitstempel)
	 *
	 * @return void Leere und offensichtlich unvollständige Zeilen werden
	 *              stillschweigend übergangen, damit eine Kopfzeile oder eine
	 *              Leerzeile am Dateiende den Import nicht abbricht
	 */
	private function importiereDatensatz(string $zeile, array $daten): void
	{
		if ('' === $zeile)
		{
			return;
		}

		$spalte = explode(';', $zeile);

		// Ohne die acht erwarteten Spalten ist die Zeile keine Wertungszeile
		if (count($spalte) < 8)
		{
			return;
		}

		$spielername = explode(',', $spalte[3]);
		$nachname = trim($spielername[0]);
		$vorname = isset($spielername[1]) ? trim($spielername[1]) : '';

		$objPlayer = Database::getInstance()->prepare('SELECT * FROM tl_fernschach_iccf_players WHERE iccfid = ?')
		                                    ->execute($spalte[0]);

		if ($objPlayer->numRows)
		{
			$playerId = $objPlayer->id;

			// Nur eine jüngere Liste darf die Stammdaten überschreiben
			if ($daten['listDate'] > $objPlayer->tstamp)
			{
				$change = '';

				if ($objPlayer->country != $spalte[1])
				{
					$change .= '['.date('d.m.Y H:i').'] '.$objPlayer->country.' &#10132; '.$spalte[1]."\n";
				}

				if ($objPlayer->surname != $nachname)
				{
					$change .= '['.date('d.m.Y H:i').'] '.$objPlayer->surname.' &#10132; '.$nachname."\n";
				}

				if ($objPlayer->prename != $vorname)
				{
					$change .= '['.date('d.m.Y H:i').'] '.$objPlayer->prename.' &#10132; '.$vorname."\n";
				}

				if ($change)
				{
					Database::getInstance()->prepare('UPDATE tl_fernschach_iccf_players %s WHERE id = ?')
					                       ->set(array
					                       (
					                       	'tstamp'  => time(),
					                       	'country' => $spalte[1],
					                       	'surname' => $nachname,
					                       	'prename' => $vorname,
					                       	'intern'  => $objPlayer->intern.$change,
					                       ))
					                       ->execute($objPlayer->id);
				}
			}
		}
		else
		{
			$objInsert = Database::getInstance()->prepare('INSERT INTO tl_fernschach_iccf_players %s')
			                                    ->set(array
			                                    (
			                                    	'tstamp'    => time(),
			                                    	'iccfid'    => $spalte[0],
			                                    	'country'   => $spalte[1],
			                                    	'surname'   => $nachname,
			                                    	'prename'   => $vorname,
			                                    	'intern'    => null,
			                                    	'published' => '1',
			                                    ))
			                                    ->execute();

			$playerId = $objInsert->insertId;
		}

		// Wertungszahl eintragen oder aktualisieren
		$set = array
		(
			'tstamp'          => time(),
			'pid'             => $playerId,
			'listId'          => $daten['listId'],
			'ratingDate'      => $daten['listDate'],
			'title'           => $spalte[2],
			'ratingDeviation' => $spalte[6],
			'flag'            => $spalte[7],
			'rating'          => $spalte[5],
			'games'           => $spalte[4],
			'published'       => '1',
		);

		$objRating = Database::getInstance()->prepare('SELECT id FROM tl_fernschach_iccf_ratings WHERE listId = ? AND pid = ?')
		                                    ->execute($daten['listId'], $playerId);

		if (!$objRating->numRows)
		{
			Database::getInstance()->prepare('INSERT INTO tl_fernschach_iccf_ratings %s')
			                       ->set($set)
			                       ->execute();

			return;
		}

		// Es kann durch frühere Importe mehr als einen Datensatz geben; alle
		// werden auf denselben Stand gebracht.
		while ($objRating->next())
		{
			Database::getInstance()->prepare('UPDATE tl_fernschach_iccf_ratings %s WHERE id = ?')
			                       ->set($set)
			                       ->execute($objRating->id);
		}
	}
}
