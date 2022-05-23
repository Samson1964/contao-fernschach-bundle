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
			// alle aktiven Einladungsturniere mit Startdatum in der Vergangenheit gewünscht
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE published = ? AND startDate <= ? AND typ = ? ORDER BY startDate ASC")
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
				$objSpieler = \Database::getInstance()->prepare("SELECT m.id AS mitglied_id, m.nachname AS nachname, m.vorname AS vorname, b.id AS bewerbung_id, b.applicationDate AS bewerbungsdatum, b.state AS status, b.stateOrganizer as veranstalter, b.promiseDate AS zusagedatum FROM tl_fernschach_turniere_bewerbungen AS b LEFT JOIN tl_fernschach_spieler AS m ON b.spielerId = m.id WHERE b.pid=? AND b.state=? AND b.stateOrganizer=? ORDER BY m.nachname ASC, m.vorname ASC")
				                                      ->execute($objTurnier->id, 1, 1);

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
					'name'            => $objTurnier->title,
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
