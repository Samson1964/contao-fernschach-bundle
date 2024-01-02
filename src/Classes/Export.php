<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class dsb_trainerlizenzExport
  */
class Export extends \Backend
{

	/**
	 * Funktion exportTrainer_XLS
	 * @param object
	 * @return string
	 */

	public function exportXLS(\DataContainer $dc)
	{
		if ($this->Input->get('key') != 'exportXLS')
		{
			return '';
		}

		$arrExport = self::getRecords($dc); // Spieler auslesen
		$recordCount = count($arrExport) + 1;

		// Neues Excel-Objekt erstellen
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		// Dokument-Eigenschaften setzen
		$spreadsheet->getProperties()->setCreator('ContaoFernschachBundle')
		            ->setLastModifiedBy('ContaoFernschachBundle')
		            ->setTitle('Spieler Deutscher Fernschachbund')
		            ->setSubject('Spieler Deutscher Fernschachbund')
		            ->setDescription('Export der Spieler im Deutschen Fernschachbund')
		            ->setKeywords('export spieler bdf fernschachbund')
		            ->setCategory('Export Spieler BdF');

		// Tabellenblätter definieren
		$sheets = array('Spieler');
		$styleArray = [
		    'font' => [
		        'bold' => true,
		    ],
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		    ],
		    'borders' => [
		        'bottom' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'argb' => 'FFA0A0A0',
		        ],
		        'endColor' => [
		            'argb' => 'FFFFFFFF',
		        ],
		    ],
		];
		$styleArray2 = [
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
		    ],
		];

		// Preise-Tabelle anlegen und füllen
		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(0);
		$spreadsheet->getActiveSheet()->getStyle('A1:AU1')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A2:AU'.$recordCount)->applyFromArray($styleArray2); // Zeilen mit Datensätzen formatieren
		$spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray); // Um Markierung zurückzusetzen
		$spreadsheet->getActiveSheet()->setTitle('Spieler')
		            ->setCellValue('A1', 'Datensatz')
		            ->setCellValue('B1', 'Letzte Änderung')
		            ->setCellValue('C1', 'Archiviert')
		            ->setCellValue('D1', 'Kenncode')
		            ->setCellValue('E1', 'Name')
		            ->setCellValue('F1', 'Vorname')
		            ->setCellValue('G1', 'Titel')
		            ->setCellValue('H1', 'Anrede')
		            ->setCellValue('I1', 'Briefanrede')
		            ->setCellValue('J1', 'Geburtsdatum')
		            ->setCellValue('K1', 'Geburtsort')
		            ->setCellValue('L1', 'Geschlecht')
		            ->setCellValue('M1', 'Verstorben')
		            ->setCellValue('N1', 'Sterbedatum')
		            ->setCellValue('O1', 'Sterbeort')
		            ->setCellValue('P1', 'PLZ')
		            ->setCellValue('Q1', 'Ort')
		            ->setCellValue('R1', 'Straße')
		            ->setCellValue('S1', 'Zusatz')
		            ->setCellValue('T1', 'PLZ 2')
		            ->setCellValue('U1', 'Ort 2')
		            ->setCellValue('V1', 'Straße 2')
		            ->setCellValue('W1', 'Zusatz 2')
		            ->setCellValue('X1', 'Telefon')
		            ->setCellValue('Y1', 'Telefon 2')
		            ->setCellValue('Z1', 'Telefax')
		            ->setCellValue('AA1', 'Telefax 2')
		            ->setCellValue('AB1', 'E-Mail')
		            ->setCellValue('AC1', 'E-Mail 2')
		            ->setCellValue('AD1', 'BdF-Nummer')
		            ->setCellValue('AE1', 'ICCF-Nummer')
		            ->setCellValue('AF1', 'Streichdatum')
		            ->setCellValue('AG1', 'Mitgliedschaft Beginn')
		            ->setCellValue('AH1', 'Mitgliedschaft Ende')
		            ->setCellValue('AI1', 'Verein')
		            ->setCellValue('AJ1', 'Status')
		            ->setCellValue('AK1', 'Zuzug')
		            ->setCellValue('AL1', 'Klassenberechtigung')
		            ->setCellValue('AM1', 'Gast-Nr.')
		            ->setCellValue('AN1', 'Servertester-Nr.')
		            ->setCellValue('AO1', 'Fremdspieler-Nr.')
		            ->setCellValue('AP1', 'Inhaber')
		            ->setCellValue('AQ1', 'IBAN')
		            ->setCellValue('AR1', 'BIC')
		            ->setCellValue('AS1', 'Saldo')
		            ->setCellValue('AT1', 'Veröffentlicht')
		            ->setCellValue('AU1', 'Fertig');

		// Daten schreiben
		$zeile = 2;
		foreach($arrExport as $item)
		{
			$spreadsheet->getActiveSheet()
			            ->getStyle('AS'.$zeile, $item['saldo'])->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
			$spreadsheet->getActiveSheet()
			            ->setCellValue('A'.$zeile, $item['id'])
			            ->setCellValue('B'.$zeile, $item['tstamp'])
			            ->setCellValue('C'.$zeile, $item['archived'])
			            ->setCellValue('D'.$zeile, $item['kenncode'])
			            ->setCellValue('E'.$zeile, $item['nachname'])
			            ->setCellValue('F'.$zeile, $item['vorname'])
			            ->setCellValue('G'.$zeile, $item['titel'])
			            ->setCellValue('H'.$zeile, $item['anrede'])
			            ->setCellValue('I'.$zeile, $item['briefanrede'])
			            ->setCellValue('J'.$zeile, $item['birthday'])
			            ->setCellValue('K'.$zeile, $item['birthplace'])
			            ->setCellValue('L'.$zeile, $item['sex'])
			            ->setCellValue('M'.$zeile, $item['death'])
			            ->setCellValue('N'.$zeile, $item['deathday'])
			            ->setCellValue('O'.$zeile, $item['deathplace'])
			            ->setCellValue('P'.$zeile, $item['plz'])
			            ->setCellValue('Q'.$zeile, $item['ort'])
			            ->setCellValue('R'.$zeile, $item['strasse'])
			            ->setCellValue('S'.$zeile, $item['adresszusatz'])
			            ->setCellValue('T'.$zeile, $item['plz2'])
			            ->setCellValue('U'.$zeile, $item['ort2'])
			            ->setCellValue('V'.$zeile, $item['strasse2'])
			            ->setCellValue('W'.$zeile, $item['adresszusatz2'])
			            ->setCellValue('X'.$zeile, $item['telefon1'])
			            ->setCellValue('Y'.$zeile, $item['telefon2'])
			            ->setCellValue('Z'.$zeile, $item['telefax1'])
			            ->setCellValue('AA'.$zeile, $item['telefax2'])
			            ->setCellValue('AB'.$zeile, $item['email1'])
			            ->setCellValue('AC'.$zeile, $item['email2'])
			            ->setCellValue('AD'.$zeile, $item['memberId'])
			            ->setCellValue('AE'.$zeile, $item['memberInternationalId'])
			            ->setCellValue('AF'.$zeile, $item['streichung'])
			            ->setCellValue('AG'.$zeile, $item['mitglied_beginn'])
			            ->setCellValue('AH'.$zeile, $item['mitglied_ende'])
			            ->setCellValue('AI'.$zeile, $item['verein'])
			            ->setCellValue('AJ'.$zeile, $item['status'])
			            ->setCellValue('AK'.$zeile, $item['zuzug'])
			            ->setCellValue('AL'.$zeile, $item['klassenberechtigung'])
			            ->setCellValue('AM'.$zeile, $item['gastNummer'])
			            ->setCellValue('AN'.$zeile, $item['servertesterNummer'])
			            ->setCellValue('AO'.$zeile, $item['fremdspielerNummer'])
			            ->setCellValue('AP'.$zeile, $item['inhaber'])
			            ->setCellValue('AQ'.$zeile, $item['iban'])
			            ->setCellValue('AR'.$zeile, $item['bic'])
			            ->setCellValue('AS'.$zeile, $item['saldo'])
			            ->setCellValue('AT'.$zeile, $item['published'])
			            ->setCellValue('AU'.$zeile, $item['fertig']);
			$zeile++;
		}

		// Spaltenbreite automatisch einstellen
		foreach($spreadsheet->getActiveSheet()->getColumnIterator() as $column)
		{
			$spreadsheet->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
		}

		$spreadsheet->getActiveSheet()->freezePane('G2'); // Bereich einfrieren/fixieren
		$spreadsheet->setActiveSheetIndex(0);

		// Überflüssiges Tabellenblatt 'Worksheet 1' löschen
		$sheetIndex = $spreadsheet->getIndex(
		    $spreadsheet->getSheetByName('Worksheet 1')
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);

		$dateiname = 'Spieler_BdF_'.date('Ymd-Hi').'.xls';

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);

		// Redirect output to a client’s web browser (Xls)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$dateiname.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
		$writer->save('php://output'); // An Browser schicken

	}

	public function getRecords(\DataContainer $dc)
	{
		// Liest die Datensätze der Fernschachverwaltung in ein Array

		// Suchbegriff in aktueller Ansicht laden
		$search = $dc->Session->get('search');
		$search = $search[$dc->table]; // Das Array enthält field und value
		//if($search['field']) $sql = " WHERE ".$search['field']." LIKE '%%".$search['value']."%%'"; // findet auch Umlaute, Suche nach "ba" findet auch "bä"
		if($search['field'] && $search['value']) $sql = " WHERE LOWER(CAST(".$search['field']." AS CHAR)) REGEXP LOWER('".$search['value']."')"; // Contao-Standard, ohne Umlaute, Suche nach "ba" findet nicht "bä"
		else $sql = '';

		// Filter in aktueller Ansicht laden. Beispiel mit Spezialfilter (tli_filter):
		//
		// [filter] => Array
		//       (
		//           [tl_lizenzverwaltungFilter] => Array
		//               (
		//                   [tli_filter] => V2
		//               )
		//
		//           [tl_lizenzverwaltung] => Array
		//               (
		//                   [limit] => 0,30
		//                   [geschlecht] => w
		//               )
		//
		//       )
		$filter = $dc->Session->get('filter');
		$filter = $filter[$dc->table]; // Das Array enthält limit (Wert meistens = 0,30) und alle Feldnamen mit den Werten
		foreach($filter as $key => $value)
		{
			if($key != 'limit')
			{
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " ".$key." = '".$value."'";
			}
		}

		// Spezialfilter berücksichtigen
		$filter = $dc->Session->get('filter');
		$filter = isset($filter[$dc->table.'Filter']['tfs_filter']) ? $filter[$dc->table.'Filter']['tfs_filter'] : ''; // Wert aus Spezialfilter
		switch($filter)
		{
			case '2': // Geburtsdatum fehlt
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " birthday = 0 OR birthday = ''";
				break;

			case '3': // ICCF-Nummer fehlt
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " memberInternationalId = ''";
				break;

			case '4': // E-Mail fehlt
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " email1 = '' AND email2 = ''";
				break;

			default:
		}

		($sql) ? $sql .= " ORDER BY nachname,vorname ASC" : $sql = " ORDER BY nachname,vorname ASC";

		$sql = "SELECT * FROM tl_fernschach_spieler".$sql;

		log_message('Excel-Export mit: '.$sql, 'fernschachverwaltung.log');
		// Datensätze laden
		$records = \Database::getInstance()->prepare($sql)
		                                   ->execute();

		// Datensätze umwandeln
		$arrExport = array();
		if($records->numRows)
		{
			while($records->next())
			{
				$exportieren = true;
				switch($filter)
				{
					case '1': // Alle Mitglieder
						// Mitgliedschaften prüfen (memberships)
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($records->memberships);
						break;

					case '8': // Alle Nichtmitglieder
						// Nichtmitgliedschaften prüfen (memberships)
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($records->memberships);
						// Wahr/Falsch umdrehen
						if($exportieren) $exportieren = false;
						else $exportieren = true;
						break;

					case '101': // Mitgliedsende 31.12. nächstes Jahr
					case '100': // Mitgliedsende 31.12. dieses Jahr
					case '99': // Mitgliedsende 31.12. letztes Jahr
					case '98': // Mitgliedsende 31.12. minus 2 Jahre
					case '97': // Mitgliedsende 31.12. minus 3 Jahre
					case '96': // Mitgliedsende 31.12. minus 4 Jahre
					case '95': // Mitgliedsende 31.12. minus 5 Jahre
					case '94': // Mitgliedsende 31.12. minus 6 Jahre
					case '93': // Mitgliedsende 31.12. minus 7 Jahre
					case '92': // Mitgliedsende 31.12. minus 8 Jahre
					case '91': // Mitgliedsende 31.12. minus 9 Jahre
						// Mitgliedsende prüfen (memberships)
						$datum = (date('Y') + $filter - 100).'1231';
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchMembership($records->memberships, $datum);
						break;

					case '201': // Nicht Mitglied nach dem 31.12. nächstes Jahr
					case '200': // Nicht Mitglied nach dem 31.12. dieses Jahr
					case '199': // Nicht Mitglied nach dem 31.12. letztes Jahr
					case '198': // Nicht Mitglied nach dem 31.12. minus 2 Jahre
					case '197': // Nicht Mitglied nach dem 31.12. minus 3 Jahre
					case '196': // Nicht Mitglied nach dem 31.12. minus 4 Jahre
					case '195': // Nicht Mitglied nach dem 31.12. minus 5 Jahre
					case '194': // Nicht Mitglied nach dem 31.12. minus 6 Jahre
					case '193': // Nicht Mitglied nach dem 31.12. minus 7 Jahre
					case '192': // Nicht Mitglied nach dem 31.12. minus 8 Jahre
					case '191': // Nicht Mitglied nach dem 31.12. minus 9 Jahre
						// Mitgliedsende prüfen (memberships)
						$datum = (date('Y') + $filter - 200).'1231';
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchNoMembership($records->memberships, $datum);
						break;

					default:
				}
				if($exportieren)
				{
					$saldo = end(\Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($records->id));
					$arrExport[] = array
					(
						'id'                      => $records->id,
						'tstamp'                  => $records->tstamp ? date("d.m.Y H:i:s",$records->tstamp) : '',
						'archived'                => $records->archived,
						'kenncode'                => self::getCode($records->id, \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($records->birthday), $records->memberId),
						'nachname'                => $records->nachname,
						'vorname'                 => $records->vorname,
						'titel'                   => $records->titel,
						'anrede'                  => $records->anrede,
						'briefanrede'             => $records->briefanrede,
						'birthday'                => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($records->birthday),
						'birthplace'              => $records->birthplace,
						'sex'                     => $records->sex,
						'death'                   => $records->death,
						'deathday'                => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($records->deathday),
						'deathplace'              => $records->deathplace,
						'plz'                     => $records->plz,
						'ort'                     => $records->ort,
						'strasse'                 => $records->strasse,
						'adresszusatz'            => $records->adresszusatz,
						'plz2'                    => $records->plz2,
						'ort2'                    => $records->ort2,
						'strasse2'                => $records->strasse2,
						'adresszusatz2'           => $records->adresszusatz2,
						'telefon1'                => $records->telefon1,
						'telefon2'                => $records->telefon2,
						'telefax1'                => $records->telefax1,
						'telefax2'                => $records->telefax2,
						'email1'                  => $records->email1,
						'email2'                  => $records->email2,
						'memberId'                => $records->memberId,
						'memberInternationalId'   => $records->memberInternationalId,
						'streichung'              => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($records->streichung),
						'mitglied_beginn'         => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate(\Schachbulle\ContaoFernschachBundle\Classes\Helper::Mitgliedschaft($records->memberships, 1)),
						'mitglied_ende'           => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate(\Schachbulle\ContaoFernschachBundle\Classes\Helper::Mitgliedschaft($records->memberships, 2)),
						'verein'                  => $records->verein,
						'status'                  => $records->status,
						'zuzug'                   => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($records->zuzug),
						'klassenberechtigung'     => $records->klassenberechtigung,
						'gastNummer'              => $records->gastNummer,
						'servertesterNummer'      => $records->servertesterNummer,
						'fremdspielerNummer'      => $records->fremdspielerNummer,
						'inhaber'                 => $records->inhaber,
						'iban'                    => $records->iban,
						'bic'                     => $records->bic,
						'saldo'                   => $saldo ? sprintf("%01.2f", $saldo) : '',
						'published'               => $records->published,
						'fertig'                  => $records->fertig,
					);
				}
			}
		}
		return $arrExport;
	}

	/**
	 * Datumswert aus Datenbank umwandeln
	 * @param mixed
	 * @return mixed
	 */
	public function getDate($varValue)
	{
		return trim($varValue) ? date('d.m.Y', $varValue) : '';
	}

	public function getCode($id, $birthday, $memberid)
	{
		$zeit = time();
		$temp = (string)$id.(string)$birthday.(string)$memberid.(string)$zeit;
		$hash = substr(hash('md5', $temp), 0, 8);
		return $hash;
	}

}
