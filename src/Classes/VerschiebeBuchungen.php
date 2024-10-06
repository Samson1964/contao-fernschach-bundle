<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class VerschibeBuchungen
  */
class VerschiebeBuchungen extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Importiert eine Buchungsliste
	 */
	public function run()
	{

		if(\Input::get('key') != 'verschiebeBuchungen')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');

		$verwendungszweck = array();
		// Verwendungszwecke finden und sortieren nach Anzahl Vorkommen
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				$vz = strtolower($objBuchungen->verwendungszweck);
				if(isset($verwendungszweck[$vz]))
				{
					$verwendungszweck[$vz]++;
				}
				else
				{
					$verwendungszweck[$vz] = 1;
				}
			}
			arsort($verwendungszweck); // Array nach Werten absteigend sortieren
		}
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_beitrag")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				$vz = strtolower($objBuchungen->verwendungszweck);
				if(isset($verwendungszweck[$vz]))
				{
					$verwendungszweck[$vz]++;
				}
				else
				{
					$verwendungszweck[$vz] = 1;
				}
			}
			arsort($verwendungszweck); // Array nach Werten absteigend sortieren
		}
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld")
		                                        ->execute();
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				$vz = strtolower($objBuchungen->verwendungszweck);
				if(isset($verwendungszweck[$vz]))
				{
					$verwendungszweck[$vz]++;
				}
				else
				{
					$verwendungszweck[$vz] = 1;
				}
			}
			arsort($verwendungszweck); // Array nach Werten absteigend sortieren
		}

		// Verwendungszweck modifizieren: array('Titel' => 'Anzahl') wird array('Titel' => 'Titel (Anzahl mal)')
		foreach($verwendungszweck as $key => $value)
		{
			$verwendungszweck[$key] = $key.' ('.$value.' mal)';
		}
		
		$form = new \Schachbulle\ContaoFernschachBundle\Classes\DCAParser('tl_dca');
		$form->setBacklink(ampersand(str_replace('&key=verschiebeBuchungen', '', \Environment::get('request'))));   
		$dca = array
		(
			'submit' => 'Verschiebung starten',
			'info' => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['hinweis'],
			'fieldsets' => array
			(
				'title_legend' => array
				(
					'title' => 'Buchungsdatum und Verwendungszweck',
					'fields' => array
					(
						'datum' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['datum'],
							'inputType'  => 'text',
							'eval'       => array('tl_class' => 'w50 widget wizard', 'datepicker'=>true, 'rgxp'=>'date', 'mandatory'=>false)
						),
						'search_verwendungszweck' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['search_verwendungszweck'],
							'inputType'  => 'text',
							'eval'       => array('tl_class' => 'w50 clr widget')
						),
						'select_verwendungszweck' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['select_verwendungszweck'],
							'inputType'  => 'select',
							'options'    => $verwendungszweck,
							'eval'       => array('tl_class' => 'w50 widget', 'chosen'=>true, 'includeBlankOption'=>true)
						)
					)
				),
				'konto_legend' => array
				(
					'title' => 'Zielkonto',
					'fields' => array
					(
						'zielkonto' => array
						(
							'label'      => &$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['zielkonto'],
							'inputType'  => 'select',
							'options'    => array('h'=>'Hauptkonto','b'=>'Beitragskonto','n'=>'Nenngeldkonto'),
							'eval'       => array('tl_class' => 'w50 widget', 'chosen'=>true, 'includeBlankOption'=>true)
						),
					),
				),
			)
		);
		$form->setDCA($dca);
		// Formular wurde abgeschickt und ist korrekt
		//if($form->isSubmitted() && $form->validate())
		if($form->isSubmitted())
		{
			$daten = $form->getData();
			$return = self::getImport($daten); // Daten sichern
			// Seite neu laden
			// Cookie setzen und zurückkehren zur Buchungsliste
			\Message::addConfirmation('Buchungen global verschieben: '.$return[0].' gefunden, '.$return[1].' verschoben.');
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			\Controller::redirect(str_replace('&key=verschiebeBuchungen', '', \Environment::get('request')));
		}
		return $form->parse();

	}

	public function getImport($daten)
	{
		print_r($daten);

		$sql = '';
		// Datum gesetzt
		if($daten['datum'])
		{
			$datum = mktime(0, 0, 0, (int)substr($daten['datum'], 3, 2), (int)substr($daten['datum'], 0, 2), (int)substr($daten['datum'], 6, 4));
			if($sql) $sql .= ' AND';
			$sql .= ' datum = '.$datum;
		}
		else $datum = false;

		// Verwendungszwecksuche gesetzt
		if($daten['search_verwendungszweck']) 
		{
			$verwendungszweck_wort = $daten['search_verwendungszweck'];
			if($sql) $sql .= ' AND';
			$sql .= ' verwendungszweck LIKE "%'.addslashes($verwendungszweck_wort).'%"';
		}
		else $verwendungszweck_wort = false;

		// Verwendungszweckauswahl gesetzt
		if($daten['select_verwendungszweck']) 
		{
			$verwendungszweck_genau = $daten['select_verwendungszweck'];
			if($sql) $sql .= ' AND';
			$sql .= ' verwendungszweck = "'.addslashes($verwendungszweck_genau).'"';
		}
		else $verwendungszweck_genau = false;
			
		// Zielkonto gesetzt
		if($daten['zielkonto']) 
		{
			if($daten['zielkonto'] == 'h') $zielkonto = 'tl_fernschach_spieler_konto'; 
			elseif($daten['zielkonto'] == 'b') $zielkonto = 'tl_fernschach_spieler_konto_beitrag'; 
			elseif($daten['zielkonto'] == 'n') $zielkonto = 'tl_fernschach_spieler_konto_nenngeld'; 
		}
		else $zielkonto = false;
		
		$found = 0; // Anzahl gefundener Buchungen
		$moved = 0; // Anzahl verschobener Buchungen
		$max_moved = 2000; // Maximale Anzahl verschobener Buchungen
		
		if(!$datum && !$verwendungszweck_wort && !$verwendungszweck_genau) $sql = false; // Keine Suchparameter

		// Verwendungszwecke finden und sortieren nach Anzahl Vorkommen
		if($sql)
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE".$sql)
			                                        ->execute();
		}
		else
		{
			// SQL-String leer, jetzt prüfen ob Zielkonto nicht gesetzt ist
			if($zielkonto) return array($found, $moved); // Abbruch, da ein Zielkonto ausgewählt ist (alle Buchungen würden dorthin verschoben werden!)
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto")
			                                        ->execute();
		}

		if($objBuchungen)
		{
			if($objBuchungen->numRows)
			{
				log_message('Buchungen suchen: SQL ('.trim($sql).') | '.$objBuchungen->numRows.' Datensätze im Hauptkonto', 'fernschachverwaltung_buchungen.log');
				$i = 0;
				$found += $objBuchungen->numRows;
				while($objBuchungen->next())
				{
					$i++;
					$meldung = $i.': ';
					$meldung .= 'PID='.$objBuchungen->pid.' | ';
					$meldung .= date('d.m.Y', $objBuchungen->datum).' | ';
					$meldung .= 'Betrag='.$objBuchungen->betrag.' | ';
					$meldung .= 'Typ='.$objBuchungen->typ.' | ';
					$meldung .= 'Kategorie='.$objBuchungen->kategorie.' | ';
					$meldung .= 'VZ='.$objBuchungen->verwendungszweck;
					log_message($meldung, 'fernschachverwaltung_buchungen.log');
					if($zielkonto && $zielkonto != 'tl_fernschach_spieler_konto')
					{
						// Buchung in das Zielkonto verschieben, wenn Quellkonto unterschiedlich
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto', $zielkonto);
						$moved++;
					}
					elseif(!$zielkonto && $objBuchungen->kategorie == 'b')
					{
						// Zielkonto nicht definiert, Hauptkonto-Buchung ist Kategorie Beitrag => verschieben in Beitragskonto
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto', 'tl_fernschach_spieler_konto_beitrag');
						$moved++;
					}
					elseif(!$zielkonto && $objBuchungen->kategorie == 's')
					{
						// Zielkonto nicht definiert, Hauptkonto-Buchung ist Kategorie Nenngeld => verschieben in Nenngeldkonto
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto', 'tl_fernschach_spieler_konto_nenngeld');
						$moved++;
					}
					if($moved >= $max_moved) return array($found, $moved); // Abbruch bei max. Anzahl Buchungen
				}
			}
		}

		// Verwendungszwecke finden und sortieren nach Anzahl Vorkommen
		if($sql)
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_beitrag WHERE".$sql)
			                                        ->execute();
		}
		else
		{
			// SQL-String leer, jetzt prüfen ob Zielkonto nicht gesetzt ist
			if($zielkonto) return array($found, $moved); // Abbruch, da ein Zielkonto ausgewählt ist (alle Buchungen würden dorthin verschoben werden!)
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_beitrag")
			                                        ->execute();
		}

		if($objBuchungen)
		{
			if($objBuchungen->numRows)
			{
				log_message('Buchungen suchen: SQL ('.trim($sql).') | '.$objBuchungen->numRows.' Datensätze im Beitragskonto', 'fernschachverwaltung_buchungen.log');
				$i = 0;
				$found += $objBuchungen->numRows;
				while($objBuchungen->next())
				{
					$i++;
					$meldung = $i.': ';
					$meldung .= 'PID='.$objBuchungen->pid.' | ';
					$meldung .= date('d.m.Y', $objBuchungen->datum).' | ';
					$meldung .= 'Betrag='.$objBuchungen->betrag.' | ';
					$meldung .= 'Typ='.$objBuchungen->typ.' | ';
					$meldung .= 'Kategorie='.$objBuchungen->kategorie.', ';
					$meldung .= 'VZ='.$objBuchungen->verwendungszweck;
					log_message($meldung, 'fernschachverwaltung_buchungen.log');
					if($zielkonto && $zielkonto != 'tl_fernschach_spieler_konto_beitrag')
					{
						// Buchung in das Zielkonto verschieben, wenn Quellkonto unterschiedlich
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto_beitrag', $zielkonto);
						$moved++;
					}
					elseif(!$zielkonto && $objBuchungen->kategorie == 's')
					{
						// Zielkonto nicht definiert, Beitrag-Buchung ist Kategorie Nenngeld => verschieben in Nenngeldkonto
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto_beitrag', 'tl_fernschach_spieler_konto_nenngeld');
						$moved++;
					}
					elseif(!$zielkonto && $objBuchungen->kategorie != 'b')
					{
						// Zielkonto nicht definiert, Beitrag-Buchung ist ohne Kategorie => verschieben in Hauptkonto
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto_beitrag', 'tl_fernschach_spieler_konto');
						$moved++;
					}
					if($moved >= $max_moved) return array($found, $moved); // Abbruch bei max. Anzahl Buchungen
				}
			}
		}

		// Verwendungszwecke finden und sortieren nach Anzahl Vorkommen
		if($sql)
		{
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld WHERE".$sql)
			                                        ->execute();
		}
		else
		{
			// SQL-String leer, jetzt prüfen ob Zielkonto nicht gesetzt ist
			if($zielkonto) return array($found, $moved); // Abbruch, da ein Zielkonto ausgewählt ist (alle Buchungen würden dorthin verschoben werden!)
			$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld")
			                                        ->execute();
		}

		if($objBuchungen)
		{
			if($objBuchungen->numRows)
			{
				log_message('Buchungen suchen: SQL ('.trim($sql).') | '.$objBuchungen->numRows.' Datensätze im Nenngeldkonto', 'fernschachverwaltung_buchungen.log');
				$i = 0;
				$found += $objBuchungen->numRows;
				while($objBuchungen->next())
				{
					$i++;
					$meldung = $i.': ';
					$meldung .= 'PID='.$objBuchungen->pid.' | ';
					$meldung .= date('d.m.Y', $objBuchungen->datum).' | ';
					$meldung .= 'Betrag='.$objBuchungen->betrag.' | ';
					$meldung .= 'Typ='.$objBuchungen->typ.' | ';
					$meldung .= 'Kategorie='.$objBuchungen->kategorie.' | ';
					$meldung .= 'VZ='.$objBuchungen->verwendungszweck;
					log_message($meldung, 'fernschachverwaltung_buchungen.log');
					if($zielkonto && $zielkonto != 'tl_fernschach_spieler_konto_nenngeld')
					{
						// Buchung in das Zielkonto verschieben, wenn Quellkonto unterschiedlich
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto_nenngeld', $zielkonto);
						$moved++;
					}
					elseif(!$zielkonto && $objBuchungen->kategorie == 'b')
					{
						// Zielkonto nicht definiert, Nenngeld-Buchung ist Kategorie Beitrag => verschieben in Nenngeldkonto
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto_nenngeld', 'tl_fernschach_spieler_konto_beitrag');
						$moved++;
					}
					elseif(!$zielkonto && $objBuchungen->kategorie != 's')
					{
						// Zielkonto nicht definiert, Nenngeld-Buchung ist ohne Kategorie => verschieben in Hauptkonto
						self::Verschieben($objBuchungen, 'tl_fernschach_spieler_konto_nenngeld', 'tl_fernschach_spieler_konto');
						$moved++;
					}
					if($moved >= $max_moved) return array($found, $moved); // Abbruch bei max. Anzahl Buchungen
				}
			}
		}

		return array($found, $moved);
	}

	public function Verschieben($objBuchung, $quelle, $ziel)
	{
		$set = array
		(
			'pid'              => $objBuchung->pid,
			'tstamp'           => $objBuchung->tstamp,
			'resetRecord'      => $objBuchung->resetRecord,
			'importDate'       => $objBuchung->importDate,
			'betrag'           => $objBuchung->betrag,
			'typ'              => $objBuchung->typ,
			'datum'            => $objBuchung->datum,
			'sortierung'       => $objBuchung->sortierung,
			'kategorie'        => $objBuchung->kategorie,
			'art'              => $objBuchung->art,
			'verwendungszweck' => $objBuchung->verwendungszweck,
			'markierung'       => $objBuchung->markierung,
			'saldoReset'       => $objBuchung->saldoReset,
			'turnier'          => $objBuchung->turnier,
			'comment'          => $objBuchung->comment,
			'meldungId'        => $objBuchung->meldungId,
			'published'        => $objBuchung->published
		);
		log_message('Verschiebe Buchung '.$quelle.' => '.$ziel.': '.print_r($set, true),'fernschachverwaltung_buchungen.log');
		$objInsert = \Database::getInstance()->prepare("INSERT INTO ".$ziel." %s")
		                                     ->set($set)
		                                     ->execute();
		$objDelete = \Database::getInstance()->prepare("DELETE FROM ".$quelle." WHERE id = ?")
		                                     ->execute($objBuchung->id);
		
		return;
	}

}
