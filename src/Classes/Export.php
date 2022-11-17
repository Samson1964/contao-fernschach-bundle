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
		$spreadsheet->getActiveSheet()->getStyle('A1:AR1')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A2:AR'.$recordCount)->applyFromArray($styleArray2); // Zeilen mit Datensätzen formatieren
		$spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray); // Um Markierung zurückzusetzen
		$spreadsheet->getActiveSheet()->setTitle('Spieler')
		            ->setCellValue('A1', 'Datensatz')
		            ->setCellValue('B1', 'Letzte Änderung')
		            ->setCellValue('C1', 'Archiviert')
		            ->setCellValue('D1', 'Name')
		            ->setCellValue('E1', 'Vorname')
		            ->setCellValue('F1', 'Titel')
		            ->setCellValue('G1', 'Anrede')
		            ->setCellValue('H1', 'Briefanrede')
		            ->setCellValue('I1', 'Geburtsdatum')
		            ->setCellValue('J1', 'Geburtsort')
		            ->setCellValue('K1', 'Geschlecht')
		            ->setCellValue('L1', 'Verstorben')
		            ->setCellValue('M1', 'Sterbedatum')
		            ->setCellValue('N1', 'Sterbeort')
		            ->setCellValue('O1', 'PLZ')
		            ->setCellValue('P1', 'Ort')
		            ->setCellValue('Q1', 'Straße')
		            ->setCellValue('R1', 'Zusatz')
		            ->setCellValue('S1', 'PLZ 2')
		            ->setCellValue('T1', 'Ort 2')
		            ->setCellValue('U1', 'Straße 2')
		            ->setCellValue('V1', 'Zusatz 2')
		            ->setCellValue('W1', 'Telefon')
		            ->setCellValue('X1', 'Telefon 2')
		            ->setCellValue('Y1', 'Telefax')
		            ->setCellValue('Z1', 'Telefax 2')
		            ->setCellValue('AA1', 'E-Mail')
		            ->setCellValue('AB1', 'E-Mail 2')
		            ->setCellValue('AC1', 'BdF-Nummer')
		            ->setCellValue('AD1', 'ICCF-Nummer')
		            ->setCellValue('AE1', 'Streichdatum')
		            ->setCellValue('AF1', 'Mitgliedschaft Beginn')
		            ->setCellValue('AG1', 'Mitgliedschaft Ende')
		            ->setCellValue('AH1', 'Verein')
		            ->setCellValue('AI1', 'Status')
		            ->setCellValue('AJ1', 'Zuzug')
		            ->setCellValue('AK1', 'Klassenberechtigung')
		            ->setCellValue('AL1', 'Gast-Nr.')
		            ->setCellValue('AM1', 'Servertester-Nr.')
		            ->setCellValue('AN1', 'Fremdspieler-Nr.')
		            ->setCellValue('AO1', 'Inhaber')
		            ->setCellValue('AP1', 'IBAN')
		            ->setCellValue('AQ1', 'BIC')
		            ->setCellValue('AR1', 'Veröffentlicht')
		            ->setCellValue('AS1', 'Fertig');

		// Daten schreiben
		$zeile = 2;
		foreach($arrExport as $item)
		{
			$spreadsheet->getActiveSheet()
			            ->setCellValue('A'.$zeile, $item['id'])
			            ->setCellValue('B'.$zeile, $item['tstamp'])
			            ->setCellValue('C'.$zeile, $item['archived'])
			            ->setCellValue('D'.$zeile, $item['nachname'])
			            ->setCellValue('E'.$zeile, $item['vorname'])
			            ->setCellValue('F'.$zeile, $item['titel'])
			            ->setCellValue('G'.$zeile, $item['anrede'])
			            ->setCellValue('H'.$zeile, $item['briefanrede'])
			            ->setCellValue('I'.$zeile, $item['birthday'])
			            ->setCellValue('J'.$zeile, $item['birthplace'])
			            ->setCellValue('K'.$zeile, $item['sex'])
			            ->setCellValue('L'.$zeile, $item['death'])
			            ->setCellValue('M'.$zeile, $item['deathday'])
			            ->setCellValue('N'.$zeile, $item['deathplace'])
			            ->setCellValue('O'.$zeile, $item['plz'])
			            ->setCellValue('P'.$zeile, $item['ort'])
			            ->setCellValue('Q'.$zeile, $item['strasse'])
			            ->setCellValue('R'.$zeile, $item['adresszusatz'])
			            ->setCellValue('S'.$zeile, $item['plz2'])
			            ->setCellValue('T'.$zeile, $item['ort2'])
			            ->setCellValue('U'.$zeile, $item['strasse2'])
			            ->setCellValue('V'.$zeile, $item['adresszusatz2'])
			            ->setCellValue('W'.$zeile, $item['telefon1'])
			            ->setCellValue('X'.$zeile, $item['telefon2'])
			            ->setCellValue('Y'.$zeile, $item['telefax1'])
			            ->setCellValue('Z'.$zeile, $item['telefax2'])
			            ->setCellValue('AA'.$zeile, $item['email1'])
			            ->setCellValue('AB'.$zeile, $item['email2'])
			            ->setCellValue('AC'.$zeile, $item['memberId'])
			            ->setCellValue('AD'.$zeile, $item['memberInternationalId'])
			            ->setCellValue('AE'.$zeile, $item['streichung'])
			            ->setCellValue('AF'.$zeile, $item['mitglied_beginn'])
			            ->setCellValue('AG'.$zeile, $item['mitglied_ende'])
			            ->setCellValue('AH'.$zeile, $item['verein'])
			            ->setCellValue('AI'.$zeile, $item['status'])
			            ->setCellValue('AJ'.$zeile, $item['zuzug'])
			            ->setCellValue('AK'.$zeile, $item['klassenberechtigung'])
			            ->setCellValue('AL'.$zeile, $item['gastNummer'])
			            ->setCellValue('AM'.$zeile, $item['servertesterNummer'])
			            ->setCellValue('AN'.$zeile, $item['fremdspielerNummer'])
			            ->setCellValue('AO'.$zeile, $item['inhaber'])
			            ->setCellValue('AP'.$zeile, $item['iban'])
			            ->setCellValue('AQ'.$zeile, $item['bic'])
			            ->setCellValue('AR'.$zeile, $item['published'])
			            ->setCellValue('AS'.$zeile, $item['fertig']);
			$zeile++;
		}

		// Spaltenbreite automatisch einstellen
		foreach($spreadsheet->getActiveSheet()->getColumnIterator() as $column)
		{
			$spreadsheet->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
		}

		$spreadsheet->getActiveSheet()->freezePane('F2'); // Bereich einfrieren/fixieren
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
		log_message(print_r($filter, true), 'fernschachverwaltung.log');
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
		$filter = $filter[$dc->table.'Filter']['tfs_filter']; // Wert aus Spezialfilter
		switch($filter)
		{
			case '1': // Alle Mitglieder
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " published = 1 AND archived = '' AND status = 1";
				break;

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

					default:
				}
				if($exportieren)
				{
					$arrExport[] = array
					(
						'id'                      => $records->id,
						'tstamp'                  => date("d.m.Y H:i:s",$records->tstamp),
						'archived'                => $records->archived,
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

}
