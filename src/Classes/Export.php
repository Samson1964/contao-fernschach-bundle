<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class dsb_trainerlizenzExport
  */
class Export extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Funktion exportTrainer_XLS
	 * @param object
	 * @return string
	 */

	public function exportXLS(\DataContainer $dc)
	{
		if($this->Input->get('key') != 'exportXLS' || !$this->User->hasAccess('export', 'fernschach_spieler'))
		{
			return '';
		}

		// Formular wurde abgeschickt
		if(\Input::post('FORM_SUBMIT') == 'tl_fernschach_exportexcel')
		{
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
			$spreadsheet->getActiveSheet()->getStyle('A1:AZ1')->applyFromArray($styleArray);
			$spreadsheet->getActiveSheet()->getStyle('A2:AZ'.$recordCount)->applyFromArray($styleArray2); // Zeilen mit Datensätzen formatieren
			$spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray); // Um Markierung zurückzusetzen
			$spreadsheet->getActiveSheet()->setTitle('Spieler')
			            ->setCellValue('A1', 'Datensatz')
			            ->setCellValue('B1', 'Letzte Änderung')
			            ->setCellValue('C1', 'Archiviert')
			            ->setCellValue('D1', 'Kenncode '.\Input::post('kenncode_stichtag'))
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
			            ->setCellValue('AI1', 'Gönner')
			            ->setCellValue('AJ1', 'Ehren-M.')
			            ->setCellValue('AK1', 'Ehren-P.')
			            ->setCellValue('AL1', 'Verein')
			            ->setCellValue('AM1', 'Status')
			            ->setCellValue('AN1', 'Zuzug')
			            ->setCellValue('AO1', 'Klassenberechtigung')
			            ->setCellValue('AP1', 'Gast-Nr.')
			            ->setCellValue('AQ1', 'Servertester-Nr.')
			            ->setCellValue('AR1', 'Fremdspieler-Nr.')
			            ->setCellValue('AS1', 'Inhaber')
			            ->setCellValue('AT1', 'IBAN')
			            ->setCellValue('AU1', 'BIC')
			            ->setCellValue('AV1', 'Saldo Hauptkonto '.\Input::post('saldo_stichtag'))
			            ->setCellValue('AW1', 'Saldo Beitrag '.\Input::post('saldo_stichtag'))
			            ->setCellValue('AX1', 'Saldo Nenngeld '.\Input::post('saldo_stichtag'))
			            ->setCellValue('AY1', 'Veröffentlicht')
			            ->setCellValue('AZ1', 'Fertig');

			// Daten schreiben
			$zeile = 2;
			foreach($arrExport as $item)
			{
				$spreadsheet->getActiveSheet()
				            ->getStyle('AV'.$zeile, $item['saldo_h'])->getNumberFormat()->setFormatCode('#,##0.00_-"€"'); // vorher setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE)
				$spreadsheet->getActiveSheet()
				            ->getStyle('AW'.$zeile, $item['saldo_b'])->getNumberFormat()->setFormatCode('#,##0.00_-"€"'); // vorher setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE)
				$spreadsheet->getActiveSheet()
				            ->getStyle('AX'.$zeile, $item['saldo_n'])->getNumberFormat()->setFormatCode('#,##0.00_-"€"'); // vorher setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE)
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
				            ->setCellValue('AI'.$zeile, $item['patron'])
				            ->setCellValue('AJ'.$zeile, $item['honor_member'])
				            ->setCellValue('AK'.$zeile, $item['honor_president'])
				            ->setCellValue('AL'.$zeile, $item['verein'])
				            ->setCellValue('AM'.$zeile, $item['status'])
				            ->setCellValue('AN'.$zeile, $item['zuzug'])
				            ->setCellValue('AO'.$zeile, $item['klassenberechtigung'])
				            ->setCellValue('AP'.$zeile, $item['gastNummer'])
				            ->setCellValue('AQ'.$zeile, $item['servertesterNummer'])
				            ->setCellValue('AR'.$zeile, $item['fremdspielerNummer'])
				            ->setCellValue('AS'.$zeile, $item['inhaber'])
				            ->setCellValue('AT'.$zeile, $item['iban'])
				            ->setCellValue('AU'.$zeile, $item['bic'])
				            ->setCellValue('AV'.$zeile, $item['saldo_h'])
				            ->setCellValue('AW'.$zeile, $item['saldo_b'])
				            ->setCellValue('AX'.$zeile, $item['saldo_n'])
				            ->setCellValue('AY'.$zeile, $item['published'])
				            ->setCellValue('AZ'.$zeile, $item['fertig']);
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
			//\Message::addConfirmation('Excel-Export ausgeführt');
			//\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			//\Controller::redirect(str_replace('&key=exportXLS', '', \Environment::get('request')));

		}

		// Formularfelder generieren
		// Return form
		$ausgabe = '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=exportXLS', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_fernschach_mitgliederstatistik" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">

<div class="tl_formbody_edit">
	<input type="hidden" name="FORM_SUBMIT" value="tl_fernschach_exportexcel">
	<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

	<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['headline'].'</h2>
	<div class="tl_tbox">
		<div class="widget clr long">
			<h3>'.$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['kenncode_stichtag'][0].'</h3>
			<input type="text" name="kenncode_stichtag" value="'.(\Input::post('kenncode_stichtag') ? \Input::post('kenncode_stichtag') : date('d.m.Y')).'">
			<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['kenncode_stichtag'][1].'</p>
		</div>';
		
		if($this->User->hasAccess('saldo', 'fernschach_spieler'))
		{
			$ausgabe .= '
			<div class="widget clr long">
				<h3>'.$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['saldo_stichtag'][0].'</h3>
				<input type="text" name="saldo_stichtag" value="'.(\Input::post('saldo_stichtag') ? \Input::post('saldo_stichtag') : date('d.m.Y')).'">
				<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['saldo_stichtag'][1].'</p>
			</div>';
		}
		
		$ausgabe .= '
	</div>
</div>

<div class="tl_formbody_submit">
<div class="tl_submit_container">
	<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['start'][0]).'">
	<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['start'][1].'</p>
</div>
</div>

</form>
</div>';

		return $ausgabe;

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

		//log_message('Excel-Export mit: '.$sql, 'fernschachverwaltung.log');
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
					case '1': // Nur Mitglieder (Aktiver Mitgliedschaftszeitraum)
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
					case '190': // Nicht Mitglied nach dem 31.12. minus 10 Jahre
					case '189': // Nicht Mitglied nach dem 31.12. minus 11 Jahre
					case '188': // Nicht Mitglied nach dem 31.12. minus 12 Jahre
					case '187': // Nicht Mitglied nach dem 31.12. minus 13 Jahre
					case '186': // Nicht Mitglied nach dem 31.12. minus 14 Jahre
					case '185': // Nicht Mitglied nach dem 31.12. minus 15 Jahre
					case '184': // Nicht Mitglied nach dem 31.12. minus 16 Jahre
						// Mitgliedsende prüfen (memberships)
						$datum = (date('Y') + $filter - 200).'1231';
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchNoMembership($records->memberships, $datum);
						break;

					case '301': // Mitgliedsbeginn nächstes Jahr
					case '300': // Mitgliedsbeginn dieses Jahr
					case '299': // Mitgliedsbeginn letztes Jahr
					case '298': // Mitgliedsbeginn minus 2 Jahre
					case '297': // Mitgliedsbeginn minus 3 Jahre
					case '296': // Mitgliedsbeginn minus 4 Jahre
					case '295': // Mitgliedsbeginn minus 5 Jahre
					case '294': // Mitgliedsbeginn minus 6 Jahre
					case '293': // Mitgliedsbeginn minus 7 Jahre
					case '292': // Mitgliedsbeginn minus 8 Jahre
					case '291': // Mitgliedsbeginn minus 9 Jahre
						// Mitgliedsende prüfen (memberships)
						$jahr = (date('Y') + $filter - 300);
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::isMemberBegin($records->memberships, $jahr);
						break;

					case '406': // Keine Nenngeldzahlungen letzte 6 Monate
					case '412': // Keine Nenngeldzahlungen letzte 12 Monate
					case '424': // Keine Nenngeldzahlungen letzte 24 Monate
						$monate = $filter - 400;
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::KeineNenngeldzahlung($records->id, $monate);
						break;

					default:
				}
				if($exportieren)
				{
					$saldo_h = end(\Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($records->id, '', \Input::post('saldo_stichtag')));
					$saldo_b = end(\Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($records->id, 'beitrag', \Input::post('saldo_stichtag')));
					$saldo_n = end(\Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($records->id, 'nenngeld', \Input::post('saldo_stichtag')));
					$arrExport[] = array
					(
						'id'                      => $records->id,
						'tstamp'                  => $records->tstamp ? date("d.m.Y H:i:s",$records->tstamp) : '',
						'archived'                => $records->archived,
						'kenncode'                => self::getCode($records->id, \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($records->birthday), $records->memberId, \Input::post('kenncode_stichtag')),
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
						'patron'                  => $records->patron,
						'honor_member'            => $records->honor_member,
						'honor_president'         => $records->honor_president,
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
						'saldo_h'                 => $this->User->hasAccess('saldo', 'fernschach_spieler') ? ($saldo_h ? sprintf("%01.2f", $saldo_h) : '') : 'Zugriff gesperrt',
						'saldo_b'                 => $this->User->hasAccess('saldo', 'fernschach_spieler') ? ($saldo_b ? sprintf("%01.2f", $saldo_b) : '') : 'Zugriff gesperrt',
						'saldo_n'                 => $this->User->hasAccess('saldo', 'fernschach_spieler') ? ($saldo_n ? sprintf("%01.2f", $saldo_n) : '') : 'Zugriff gesperrt',
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

	public function getCode($id, $birthday, $memberid, $datum)
	{
		$zeit = mktime(0, 0, 0, (int)substr($datum, 3, 2), (int)substr($datum, 0, 2), (int)substr($datum, 6, 4));
		$temp = (string)$id.(string)$birthday.(string)$memberid.(string)$zeit;
		$hash = substr(hash('md5', $temp), 0, 8);
		return $hash;
	}

}
