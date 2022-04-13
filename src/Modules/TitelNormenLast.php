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

class TitelNormenLast extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_titelnormen_liste';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH-VERWALTUNG - LETZTE TITEL UND NORMEN ###';
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
		
		// Titel und Normen auslesen
		if($objMembers->numRows)
		{
			while($objMembers->next())
			{

				// Titel der Reihe nach abfragen
				foreach($GLOBALS['TL_LANG']['tl_fernschachverwaltung']['normen_titel'] as $titelkey => $titelname)
				{
					if($objMembers->{$titelkey.'_title'})
					{
						// Titel gefunden, Daten übernehmen
						$titel[] = array
						(
							'id'          => $objMembers->id,
							'nachname'    => $objMembers->nachname,
							'vorname'     => $objMembers->vorname,
							'titel'       => $titelkey,
							'datum'       => $objMembers->{$titelkey.'_date'},
							'turnier'     => '',
							'link'        => '',
						);
					}
				}
					
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

		$count = 1;
		// Titel übertragen in Ausgabe
		for($x = 0; $x < count($titel); $x++)
		{
			// Nur Titel innerhalb des Zeitfensters
			if($titel[$x]['datum'] >= $mindate && $titel[$x]['datum'] <= $maxdate)
			{
				$daten[$titel[$x]['datum'].'_'.$count] = \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($titel[$x]['datum']).': '.$titel[$x]['vorname'].' '.$titel[$x]['nachname'].' wurde der Titel '.$GLOBALS['TL_LANG']['tl_mitgliederverwaltung']['normen_titel'][$titel[$x]['titel']].' verliehen.';
				$count++;
			}
		}
		
		// Normen übertragen in Ausgabe
		for($x = 0; $x < count($normen); $x++)
		{
			// Nur Normen innerhalb des Zeitfensters
			if($normen[$x]['datum'] >= $mindate && $normen[$x]['datum'] <= $maxdate)
			{
				$daten[$normen[$x]['datum'].'_'.$count] = \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($normen[$x]['datum']).': '.$normen[$x]['vorname'].' '.$normen[$x]['nachname'].' hat eine Norm für den Titel '.$GLOBALS['TL_LANG']['tl_mitgliederverwaltung']['normen_titel'][$normen[$x]['titel']].' erreicht.';
				$count++;
 			}
		}

		// Daten abwärts sortieren
		krsort($daten);
		// Nur die Anzahl der gewünschten Daten zurückgeben
		$daten = array_splice($daten, 0, $this->fernschachverwaltung_anzahl);

		$this->Template->daten = $daten;

	}

}
