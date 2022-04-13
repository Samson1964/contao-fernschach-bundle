<?php

namespace Schachbulle\ContaoMitgliederverwaltungBundle\ContentElements;

class Zusagen extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_mitgliederverwaltung_zusagen';

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		// Person aus Datenbank laden, wenn ID übergeben wurde
		if($this->mvturnierverwaltung_id)
		{
			$objApplications = $this->Database->prepare("SELECT m.id AS mitglied_id, m.nachname AS nachname, m.vorname AS vorname, a.id AS bewerbung_id, a.applicationDate AS bewerbungsdatum, a.state AS status, a.promiseDate AS zusagedatum FROM tl_mitgliederverwaltung_applications AS a LEFT JOIN tl_mitgliederverwaltung AS m ON a.pid = m.id WHERE a.tournament=? AND a.state=? ORDER BY m.nachname ASC, m.vorname ASC")
			                                  ->execute($this->mvturnierverwaltung_id, 1);

			$daten = array();
			// Datensätze gefunden
			if($objApplications->numRows)
			{

				while($objApplications->next())
				{
					$daten[] = array
					(
						'nachname' => $objApplications->nachname,
						'vorname'  => $objApplications->vorname,
					);
				}

			}

			// Daten aus tl_content in das Template schreiben
			$this->Template->daten      = $daten;

		}

		return;

	}

}