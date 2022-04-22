<?php

namespace Schachbulle\ContaoFernschachBundle\ContentElements;

class Zusagen extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_fernschach_zusagen';

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		// Turnier(e) aus Datenbank laden
		if($this->fernschachverwaltung_id)
		{
			// Nur ein Turnier gewünscht
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
			                                      ->execute($this->fernschachverwaltung_id);
		}
		else
		{
			// alle aktiven Einladungsturniere mit Startdatum in der Zukunft gewünscht
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE published = ? AND startDate >= ? AND typ = ? ORDER BY startDate ASC")
			                                      ->execute(1, time(), 'e');
		}

		$daten = array();
		// Datensätze gefunden
		if($objTurnier->numRows)
		{

			while($objTurnier->next())
			{
				// Turnierzusagen einlesen
				$zusagen = array();
				$objSpieler = \Database::getInstance()->prepare("SELECT m.id AS mitglied_id, m.nachname AS nachname, m.vorname AS vorname, a.id AS bewerbung_id, a.applicationDate AS bewerbungsdatum, a.state AS status, a.promiseDate AS zusagedatum FROM tl_mitgliederverwaltung_applications AS a LEFT JOIN tl_mitgliederverwaltung AS m ON a.pid = m.id WHERE a.tournament=? AND a.state=? ORDER BY m.nachname ASC, m.vorname ASC")
				                                      ->execute($objTurnier->id, 1);

				if($objSpieler->numRows)
				{

					while($objSpieler->next())
					{
						$zusagen[] = array
						(
							'nachname' => $objSpieler->nachname,
							'vorname'  => $objSpieler->vorname,
						);
					}

				}

				$daten[] = array
				(
					'name'            => $objTurnier->titel,
					'applicationText' => $objTurnier->applicationText,
					'startDate'       => date('d.m.Y', $objTurnier->startDate),
					'spieler'         => $zusagen,
				);
			}

		}

		// Daten aus tl_content in das Template schreiben
		$this->Template->daten = $daten;

		return;
	}
}
