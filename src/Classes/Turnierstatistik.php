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
			$datumtext = $viewJahr;
			if($viewMonat)
			{
				$von = mktime(0, 0, 0, $viewMonat, 1, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
				$bis = mktime(23, 59, 59, $viewMonat, 31, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
				$datumtext = $Monate[$viewMonat].' '.$datumtext;
				if($viewTag)
				{
					$von = mktime(0, 0, 0, $viewMonat, $viewTag, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
					$bis = mktime(23, 59, 59, $viewMonat, $viewTag, $viewJahr); // Std, Min, Sek, Mon, Tag, Jahr
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
		$meldungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen WHERE meldungDatum >= ? AND meldungDatum <= ?")
		                                     ->execute($von, $bis);
		$meldungen_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen WHERE meldungDatum >= ? AND meldungDatum <= ? AND published = ?")
		                                                     ->execute($von, $bis, 1);
		$bewerbungen = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_bewerbungen WHERE applicationDate >= ? AND applicationDate <= ?")
		                                       ->execute($von, $bis);
		$bewerbungen_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_bewerbungen WHERE applicationDate >= ? AND applicationDate <= ? AND published = ?")
		                                                       ->execute($von, $bis, 1);

		$datumsstatistik = array
		(
			'<b>Anzahl aller Meldungen</b>' => $meldungen->anzahl,
			'&raquo; davon veröffentlicht&nbsp;' => $meldungen_veroeffentlicht->anzahl,
			'<b>Anzahl aller Bewerbungen</b>' => $bewerbungen->anzahl,
			'&raquo; davon veröffentlicht&nbsp;&nbsp;' => $bewerbungen_veroeffentlicht->anzahl,
		);

		$turniere = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere")
		                                    ->execute();
		$turniere_veroeffentlicht = \Database::getInstance()->prepare("SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere WHERE published = ?")
		                                                    ->execute(1);
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
			'<b>Anzahl aller Turniere</b>' => $turniere->anzahl,
			'&raquo; davon veröffentlicht' => $turniere_veroeffentlicht->anzahl,
			'&raquo; &raquo; davon mit Meldeschluss in der Zukunft' => $turniere_meldeschluss->anzahl,
			'<b>Anzahl aller Meldungen</b>' => $meldungen->anzahl,
			'&raquo; davon veröffentlicht&nbsp;' => $meldungen_veroeffentlicht->anzahl,
			'<b>Anzahl aller Bewerbungen</b>' => $bewerbungen->anzahl,
			'&raquo; davon veröffentlicht&nbsp;&nbsp;' => $bewerbungen_veroeffentlicht->anzahl,
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