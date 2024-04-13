<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   DeWIS
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

namespace Schachbulle\ContaoFernschachBundle\Modules;

class TitelNormen extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_titelnormen';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH-VERWALTUNG - TITEL UND NORMEN ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		// Aktive Mitglieder laden
		$objMembers = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler WHERE published = ?')
		                                      ->execute(1);

		$daten = array();
		$titel = array();
		$normen = array();
		$mindate = date('Ymd', strtotime($this->fernschachverwaltung_zeitraum)); // Nur Normen/Titel eines bestimmten Zeitraums
		$maxdate = date('Ymd'); // Heutiges Datum setzen
		
		$titel = \Schachbulle\ContaoFernschachBundle\Classes\Titel::get(); // Titel der veröffentlichten Spieler laden
		
		// Titel und Normen auslesen
		if($objMembers->numRows)
		{
			while($objMembers->next())
			{
				// Normen suchen
				$normenMember = unserialize($objMembers->normen); // Normen extrahieren
				if(is_array($normenMember))
				{
					for($x = 0; $x < count($normenMember); $x++)
					{
						if($normenMember[$x]['title'])
						{
							$normen[] = array
							(
								'id'          => $objMembers->id,
								'nachname'    => $objMembers->nachname,
								'vorname'     => $objMembers->vorname,
								'titel'       => $normenMember[$x]['title'],
								'datum'       => $normenMember[$x]['date'],
								'turnier'     => trim($normenMember[$x]['tournament']),
								'link'        => trim($normenMember[$x]['url']),
								'markiert'    => false
							);
						}
					}	
				}
			}
		}

		// Alle Titel darauf prüfen, welche Norm dazugehören könnte
		for($x = 0; $x < count($titel); $x++)
		{
			$differenz = 999999; // Differenz zwischen Titel- und Normdatum auf hohen Wert setzen
			// Normen des Spielers und seines Titels suchen
			for($y = 0; $y < count($normen); $y++)
			{
				if($titel[$x]['id'] == $normen[$y]['id'] && $titel[$x]['titel'] == $normen[$y]['titel'])
				{
					// Norm markieren
					$normen[$y]['markiert'] = true;
					// Mögliche letzte Norm dieses Titels eintragen
					if($differenz > $titel[$x]['datum'] - $normen[$y]['datum'])
					{
						$differenz = $titel[$x]['datum'] - $normen[$y]['datum'];
						$titel[$x]['turnier'] = $normen[$y]['turnier'];
						$titel[$x]['link'] = $normen[$y]['link'];
					}
				}
			}
		}

		// Titel sortieren nach Datum absteigend
		$titel = \Schachbulle\ContaoHelperBundle\Classes\Helper::sortArrayByFields($titel, array('datum' => SORT_DESC));

		// Markierte Normen entfernen, da bereits in Titeln verwendet
		$neu = array();
		for($y = 0; $y < count($normen); $y++)
		{
			if(!$normen[$y]['markiert'])
			{
				$neu[] = $normen[$y];
			}
		}
		
		// Normen sortieren nach Datum absteigend
		$normen = \Schachbulle\ContaoHelperBundle\Classes\Helper::sortArrayByFields($neu, array('datum' => SORT_DESC));

		// Titel übertragen in Ausgabe
		for($x = 0; $x < count($titel); $x++)
		{
			// Nur Titel innerhalb des Zeitfensters
			if($titel[$x]['datum'] >= $mindate && $titel[$x]['datum'] <= $maxdate)
			{
				$daten[$titel[$x]['titel']]['titel'][] = array
				(
					'nachname'    => $titel[$x]['nachname'],
					'vorname'     => $titel[$x]['vorname'],
					'name'        => $titel[$x]['vorname'].' '.$titel[$x]['nachname'],
					'norm'        => $titel[$x]['titel'],
					'datum'       => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($titel[$x]['datum']),
					'turnier'     => $titel[$x]['turnier'],
					'link'        => $titel[$x]['link'],
					'turnierlink' => $titel[$x]['link'] ? '<a href="'.$titel[$x]['link'].'" target="_blank">'.$titel[$x]['turnier'].'</a>' : $titel[$x]['turnier'],
				);
 			}
		}
		
		// Normen übertragen in Ausgabe
		for($x = 0; $x < count($normen); $x++)
		{
			// Nur Normen innerhalb des Zeitfensters
			if($normen[$x]['datum'] >= $mindate && $normen[$x]['datum'] <= $maxdate)
			{
				$daten[$normen[$x]['titel']]['norm'][] = array
				(
					'nachname'    => $normen[$x]['nachname'],
					'vorname'     => $normen[$x]['vorname'],
					'name'        => $normen[$x]['vorname'].' '.$normen[$x]['nachname'],
					'norm'        => $normen[$x]['titel'],
					'datum'       => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($normen[$x]['datum']),
					'turnier'     => $normen[$x]['turnier'],
					'link'        => $normen[$x]['link'],
					'turnierlink' => $normen[$x]['link'] ? '<a href="'.$normen[$x]['link'].'" target="_blank">'.$normen[$x]['turnier'].'</a>' : $normen[$x]['turnier'],
				);
 			}
		}

		// Ausgabe
		$this->Template->daten = $daten;

	}

}
