<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Schachbulle\ContaoFernschachBundle\Classes;

class Turnierstatistik
{

	/**
	 * Funktion Statistik
	 */
	public function Statistik()
	{

		$Monate = array
		(
			'1' => 'Januar',
			'2' => 'Februar',
			'3' => 'März',
			'4' => 'April',
			'5' => 'Mai',
			'6' => 'Juni',
			'7' => 'Juli',
			'8' => 'August',
			'9' => 'September',
			'10' => 'Oktober',
			'11' => 'November',
			'12' => 'Dezember',
		);
		
		$Template = new \BackendTemplate('be_turnierstatistik');
		$Template->request = ampersand(\Environment::getInstance()->request, true);

		// Aktuelles Datum ermitteln
		$aktJahr  = date('Y');
		$aktMonat = date('n');
		$aktTag   = date('j');

		// Datum und Datumsrichtung aus URL ermitteln
		$urlJahr = (int)\Input::get('jahr');
		$urlMonat = (int)\Input::get('monat');
		$urlTag = (int)\Input::get('tag');
		$differenz = \Input::get('differenz');

		// Neues Datum für anzuzeigende Daten ermitteln
		if(!$urlJahr && !$urlMonat && !$urlTag)
		{
			// Keine Datumsparameter gefunden, deshalb aktuellen Tag vorgeben
			$viewJahr = $aktJahr;
			$viewMonat = $aktMonat;
			$viewTag = $aktTag;
		}
		elseif($urlJahr && !$urlMonat && !$urlTag)
		{
			// Nur Jahr-Parameter gefunden, Monat und Tag auf 0 setzen
			$viewJahr = $urlJahr;
			$viewMonat = 0;
			$viewTag = 0;
			// Anderes Jahr gewünscht?
			if($differenz) $viewJahr += $differenz;
		}
		elseif($urlJahr && $urlMonat && !$urlTag)
		{
			// Nur Jahr/Monat-Parameter gefunden, Tag auf 0 setzen
			$viewJahr = $urlJahr;
			$viewMonat = $urlMonat;
			$viewTag = 0;
			// Anderer Monat gewünscht?
			if($differenz)
			{
				$viewMonat += $differenz;
				if($viewMonat == 13)
				{
					$viewMonat = 1;
					$viewJahr++;
				}
				elseif($viewMonat == 0)
				{
					$viewMonat = 12;
					$viewJahr--;
				}
			}
		}
		elseif($urlJahr && $urlMonat && $urlTag)
		{
			// Alle Datum-Parameter gefunden
			$viewJahr = $urlJahr;
			$viewMonat = $urlMonat;
			$viewTag = $urlTag;
			// Anderer Tag gewünscht?
			if($differenz)
			{
				$zeitstempel = mktime(0, 0, 0, $viewMonat, $viewTag, $viewJahr);
				$neuzeit = strtotime($differenz." day", $zeitstempel);
				$viewJahr  = date('Y', $neuzeit);
				$viewMonat = date('n', $neuzeit);
				$viewTag   = date('j', $neuzeit);
			}
		}

		// Vor- und Zurücklinks generieren
		$vorLink = '<a href="contao?do=fernschach-turniere&key=statistik&jahr='.$viewJahr.'&monat='.$viewMonat.'&tag='.$viewTag.'&differenz=1&rt='.REQUEST_TOKEN.'"><img src="bundles/contaofernschach/images/plus.png"></a>';
		$zurueckLink = '<a href="contao?do=fernschach-turniere&key=statistik&jahr='.$viewJahr.'&monat='.$viewMonat.'&tag='.$viewTag.'&differenz=-1&rt='.REQUEST_TOKEN.'"><img src="bundles/contaofernschach/images/minus.png"></a>';

		// Anzuzeigendes Datum erstellen
		if($viewJahr) $datum = $viewJahr;
		if($viewMonat) $datum = $viewMonat.'.'.$datum;
		if($viewTag) $datum = $viewTag.'.'.$datum;
		
		// Zeitraum für anzuzeigendes Datum ermitteln
		if($viewJahr)
		{
			$von = mktime(0, 0, 0, 1, 1, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
			$bis = mktime(23, 59, 59, 12, 31, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
			$referenzdatum = $viewJahr.'1231';
			$datumtext = $viewJahr;
			if($viewMonat)
			{
				$von = mktime(0, 0, 0, $viewMonat, 1, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
				$bis = mktime(23, 59, 59, $viewMonat, 31, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
				$referenzdatum = $viewJahr.str_pad($viewMonat, 2, '0', STR_PAD_LEFT).'31';
				$datumtext = $Monate[$viewMonat].' '.$datumtext;
				if($viewTag)
				{
					$von = mktime(0, 0, 0, $viewMonat, $viewTag, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
					$bis = mktime(23, 59, 59, $viewMonat, $viewTag, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
					$referenzdatum = $viewJahr.str_pad($viewMonat, 2, '0', STR_PAD_LEFT).str_pad($viewTag, 2, '0', STR_PAD_LEFT);
					$datumtext = $viewTag.'. '.$datumtext;
				}
			}
		}

		// Link-Parameter für aktuelles Jahr
		$aktJahrLink = 'jahr='.$aktJahr.'&monat=0&tag=0';
		// Link-Parameter für aktuellen Monat
		$aktMonatLink = 'jahr='.$aktJahr.'&monat='.$aktMonat.'&tag=0';
		// Link-Parameter für aktuellen Tag
		$aktTagLink = 'jahr='.$aktJahr.'&monat='.$aktMonat.'&tag='.$aktTag;

		// Datumsstatistik erstellen (für den aktuellen Zeitraum)
		$objMeldungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE meldungDatum >= ? AND meldungDatum <= ? AND published = ?")
		                                        ->execute($von, $bis, 1);
		$arrMeldungen = array
		(
			'count' => array
			(
				'all' => 0,
				'n'   => 0,
				'i'   => 0,
				'e'   => 0,
				'm'   => 0,
				''    => 0,
			),
			'players' => array
			(
				'all' => array('meldungen' => 0, 'player' => array()),
				'n'   => array('meldungen' => 0, 'player' => array()),
				'i'   => array('meldungen' => 0, 'player' => array()),
				'e'   => array('meldungen' => 0, 'player' => array()),
				'm'   => array('meldungen' => 0, 'player' => array()),
				''    => array('meldungen' => 0, 'player' => array()),
			),
		);
		if($objMeldungen->numRows)
		{
			// Veröffentlichte Meldungen für diesen Zeitraum im Detail auswerten
			while($objMeldungen->next())
			{
				// Spielerdatensatz laden
				$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
				                                      ->execute($objMeldungen->spielerId);
				// Mitgliedsstatus prüfen (hier muß noch das Datum rein)
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objSpieler, $referenzdatum);
				// Turnierdatensatz laden
				$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
				                                      ->execute($objMeldungen->pid);
				if($objTurnier->numRows)
				{
					$arrMeldungen['count']['all']++;
					$arrMeldungen['count'][$objTurnier->typ]++;
					if($mitglied)
					{
						if(isset($arrMeldungen['players']['all']['meldungen']))
						{
							$arrMeldungen['players']['all']['meldungen']++;
						}
						else
						{
							$arrMeldungen['players']['all']['meldungen'] = 1;
						}
						if(isset($arrMeldungen['players'][$objTurnier->typ]['meldungen']))
						{
							$arrMeldungen['players'][$objTurnier->typ]['meldungen']++;
						}
						else
						{
							$arrMeldungen['players'][$objTurnier->typ]['meldungen'] = 1;
						}
						// Meldungen für alle Turniere addieren
						if(isset($arrMeldungen['players']['all']['player'][$objSpieler->id]))
						{
							$arrMeldungen['players']['all']['player'][$objSpieler->id]++;
						}
						else
						{
							$arrMeldungen['players']['all']['player'][$objSpieler->id] = 1;
						}
						if(isset($arrMeldungen['players'][$objTurnier->typ]['player'][$objSpieler->id]))
						{
							$arrMeldungen['players'][$objTurnier->typ]['player'][$objSpieler->id]++;
						}
						else
						{
							$arrMeldungen['players'][$objTurnier->typ]['player'][$objSpieler->id] = 1;
						}
					}
				}
			}
		}
		$meldungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen WHERE meldungDatum >= ? AND meldungDatum <= ?")
		                                     ->execute($von, $bis);
		$meldungen_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen WHERE meldungDatum >= ? AND meldungDatum <= ? AND published = ?")
		                                                     ->execute($von, $bis, 1);
		$objBewerbungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_bewerbungen WHERE applicationDate >= ? AND applicationDate <= ? AND published = ?")
		                                          ->execute($von, $bis, 1);
		$arrBewerbungen = array
		(
			'count' => array
			(
				'all' => 0,
				'n'   => 0,
				'i'   => 0,
				'e'   => 0,
				'm'   => 0,
				''    => 0,
			),
			'players' => array
			(
				'all' => array('meldungen' => 0, 'player' => array()),
				'n'   => array('meldungen' => 0, 'player' => array()),
				'i'   => array('meldungen' => 0, 'player' => array()),
				'e'   => array('meldungen' => 0, 'player' => array()),
				'm'   => array('meldungen' => 0, 'player' => array()),
				''    => array('meldungen' => 0, 'player' => array()),
			),
		);
		if($objBewerbungen->numRows)
		{
			// Veröffentlichte Meldungen für diesen Zeitraum im Detail auswerten
			while($objBewerbungen->next())
			{
				// Spielerdatensatz laden
				$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
				                                      ->execute($objBewerbungen->spielerId);
				// Mitgliedsstatus prüfen (hier muß noch das Datum rein)
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objSpieler, $referenzdatum);
				// Turnierdatensatz laden
				$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
				                                      ->execute($objBewerbungen->pid);
				if($objTurnier->numRows)
				{
					$arrBewerbungen['count']['all']++;
					$arrBewerbungen['count'][$objTurnier->typ]++;
					if($mitglied)
					{
						if(isset($arrBewerbungen['players']['all']['meldungen']))
						{
							$arrBewerbungen['players']['all']['meldungen']++;
						}
						else
						{
							$arrBewerbungen['players']['all']['meldungen'] = 1;
						}
						if(isset($arrBewerbungen['players'][$objTurnier->typ]['meldungen']))
						{
							$arrBewerbungen['players'][$objTurnier->typ]['meldungen']++;
						}
						else
						{
							$arrBewerbungen['players'][$objTurnier->typ]['meldungen'] = 1;
						}
						// Bewerbungen für alle Turniere addieren
						if(isset($arrBewerbungen['players']['all']['player'][$objSpieler->id]))
						{
							$arrBewerbungen['players']['all']['player'][$objSpieler->id]++;
						}
						else
						{
							$arrBewerbungen['players']['all']['player'][$objSpieler->id] = 1;
						}
						if(isset($arrBewerbungen['players'][$objTurnier->typ]['player'][$objSpieler->id]))
						{
							$arrBewerbungen['players'][$objTurnier->typ]['player'][$objSpieler->id]++;
						}
						else
						{
							$arrBewerbungen['players'][$objTurnier->typ]['player'][$objSpieler->id] = 1;
						}
					}
				}
			}
		}

		if($objBewerbungen->numRows)
		{
			// Veröffentlichte Bewerbungen für diesen Zeitraum im Detail auswerten
			while($objBewerbungen->next())
			{
				$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
				                                      ->execute($objBewerbungen->pid);
				if($objTurnier->numRows)
				{
					$arrBewerbungen['count'][$objTurnier->typ]++;
				}
			}
		}
		$bewerbungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_bewerbungen WHERE applicationDate >= ? AND applicationDate <= ?")
		                                       ->execute($von, $bis);
		$bewerbungen_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_bewerbungen WHERE applicationDate >= ? AND applicationDate <= ? AND published = ?")
		                                                       ->execute($von, $bis, 1);

		$datumsstatistik = array
		(
			array('<b>Anzahl aller Meldungen</b>', $meldungen->anzahl),
			array('&raquo; davon veröffentlicht', sprintf('%s (davon %s BdF-Mitglieder mit %s Meldungen)', $meldungen_veroeffentlicht->anzahl, count($arrMeldungen['players']['all']['player']), $arrMeldungen['players']['all']['meldungen'])),
			array('&raquo; &raquo; davon für nationale Turniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Meldungen)', $arrMeldungen['count']['n'], count($arrMeldungen['players']['n']['player']), $arrMeldungen['players']['n']['meldungen'])),
			array('&raquo; &raquo; davon für internationale Turniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Meldungen)', $arrMeldungen['count']['i'], count($arrMeldungen['players']['i']['player']), $arrMeldungen['players']['i']['meldungen'])),
			array('&raquo; &raquo; davon für Einladungsturniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Meldungen)', $arrMeldungen['count']['e'], count($arrMeldungen['players']['e']['player']), $arrMeldungen['players']['e']['meldungen'])),
			array('&raquo; &raquo; davon für Mannschaftsturniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Meldungen)', $arrMeldungen['count']['m'], count($arrMeldungen['players']['m']['player']), $arrMeldungen['players']['m']['meldungen'])),
			array('&raquo; &raquo; davon für unbekannte Turniertypen', sprintf('%s (davon %s BdF-Mitglieder mit %s Meldungen)', $arrMeldungen['count'][''], count($arrMeldungen['players']['']['player']), $arrMeldungen['players']['']['meldungen'])),
			array('<b>Anzahl aller Bewerbungen</b>', $bewerbungen->anzahl),
			array('&raquo; davon veröffentlicht', sprintf('%s (davon %s BdF-Mitglieder mit %s Bewerbungen)', $bewerbungen_veroeffentlicht->anzahl, count($arrBewerbungen['players']['all']['player']), $arrBewerbungen['players']['all']['meldungen'])),
			array('&raquo; &raquo; davon für nationale Turniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Bewerbungen)', $arrBewerbungen['count']['n'], count($arrBewerbungen['players']['n']['player']), $arrBewerbungen['players']['n']['meldungen'])),
			array('&raquo; &raquo; davon für internationale Turniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Bewerbungen)', $arrBewerbungen['count']['i'], count($arrBewerbungen['players']['i']['player']), $arrBewerbungen['players']['i']['meldungen'])),
			array('&raquo; &raquo; davon für Einladungsturniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Bewerbungen)', $arrBewerbungen['count']['e'], count($arrBewerbungen['players']['e']['player']), $arrBewerbungen['players']['e']['meldungen'])),
			array('&raquo; &raquo; davon für Mannschaftsturniere', sprintf('%s (davon %s BdF-Mitglieder mit %s Bewerbungen)', $arrBewerbungen['count']['m'], count($arrBewerbungen['players']['m']['player']), $arrBewerbungen['players']['m']['meldungen'])),
			array('&raquo; &raquo; davon für unbekannte Turniertypen', sprintf('%s (davon %s BdF-Mitglieder mit %s Bewerbungen)', $arrBewerbungen['count'][''], count($arrBewerbungen['players']['']['player']), $arrBewerbungen['players']['']['meldungen'])),
		);

		$turniere = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere")
		                                    ->execute();
		$turniere_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ?")
		                                                    ->execute(1);
		$turniere_national = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ? AND typ = ?")
		                                             ->execute(1, 'n');
		$turniere_international = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ? AND typ = ?")
		                                                  ->execute(1, 'i');
		$turniere_einladungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ? AND typ = ?")
		                                                ->execute(1, 'e');
		$turniere_mannschaften = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ? AND typ = ?")
		                                             ->execute(1, 'm');
		$turniere_sonstige = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ? AND typ = ?")
		                                             ->execute(1, '');
		$turniere_meldeschluss = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE registrationDate >= ?")
		                                                 ->execute(time());
		$meldungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen")
		                                     ->execute();
		$meldungen_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen WHERE published = ?")
		                                                     ->execute(1);
		$bewerbungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_bewerbungen")
		                                       ->execute();
		$bewerbungen_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_bewerbungen WHERE published = ?")
		                                                       ->execute(1);

		$gesamtstatistik = array
		(
			array('<b>Anzahl aller Turniere</b>', $turniere->anzahl),
			array('&raquo; davon veröffentlicht', $turniere_veroeffentlicht->anzahl),
			array('&raquo; &raquo; davon nationale Turniere', $turniere_national->anzahl),
			array('&raquo; &raquo; davon internationale Turniere', $turniere_international->anzahl),
			array('&raquo; &raquo; davon Einladungsturniere', $turniere_einladungen->anzahl),
			array('&raquo; &raquo; davon Mannschaftsturniere', $turniere_mannschaften->anzahl),
			array('&raquo; &raquo; davon unbekannte Turnierart', $turniere_sonstige->anzahl),
			array('&raquo; &raquo; davon mit Meldeschluss in der Zukunft', $turniere_meldeschluss->anzahl),
			array('<b>Anzahl aller Meldungen</b>', $meldungen->anzahl),
			array('&raquo; davon veröffentlicht', $meldungen_veroeffentlicht->anzahl),
			array('<b>Anzahl aller Bewerbungen</b>', $bewerbungen->anzahl),
			array('&raquo; davon veröffentlicht', $bewerbungen_veroeffentlicht->anzahl),
		);

		$Template->Datumsstatistik = $datumsstatistik;
		$Template->Gesamtstatistik = $gesamtstatistik;
		$Template->Datum = $datum;
		$Template->Datumtext = $datumtext;
		$Template->VorLink = $vorLink;
		$Template->ZurueckLink = $zurueckLink;
		$Template->LinkAktuellesJahr = '<a href="contao?do=fernschach-turniere&key=statistik&'.$aktJahrLink.'&rt='.REQUEST_TOKEN.'">'.$aktJahr.'</a>';
		$Template->LinkAktuellerMonat = '<a href="contao?do=fernschach-turniere&key=statistik&'.$aktMonatLink.'&rt='.REQUEST_TOKEN.'">'.$aktMonat.'.'.$aktJahr.'</a>';
		$Template->LinkAktuellerTag = '<a href="contao?do=fernschach-turniere&key=statistik&'.$aktTagLink.'&rt='.REQUEST_TOKEN.'">'.$aktTag.'.'.$aktMonat.'.'.$aktJahr.'</a>';

		return $Template->parse();
	}

}