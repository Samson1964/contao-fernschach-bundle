<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class Statistik
  */
class Statistik extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Importiert eine Mitgliederliste
	 */
	public function run()
	{

		if(\Input::get('key') != 'statistik')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');

		// Formular wurde abgeschickt, Wortliste importieren
		if(\Input::post('FORM_SUBMIT') == 'tl_fernschach_mitgliederstatistik')
		{
			$statistik = self::getStatistik(\Input::post('stichtag'), \Input::post('altersstruktur')); // Statistik ermitteln
			$recordCount = '';

			//print_r($statistik);

			// Neues Excel-Objekt erstellen
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

			// Dokument-Eigenschaften setzen
			$spreadsheet->getProperties()->setCreator('ContaoFernschachBundle')
			            ->setLastModifiedBy('ContaoFernschachBundle')
			            ->setTitle('Mitgliederstatistik Deutscher Fernschachbund')
			            ->setSubject('Mitgliederstatistik Deutscher Fernschachbund')
			            ->setDescription('Mitgliederstatistik im Deutschen Fernschachbund')
			            ->setKeywords('statistik mitglieder bdf fernschachbund')
			            ->setCategory('Mitgliederstatistik BdF');

			// Tabellenblätter definieren
			$sheets = array('Statistik');
			$styleArray = [
			    'font' => [
			        'bold' => true,
			        'size' => 18,
			        'color' => [
			             'argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE,
			        ]
			    ],
			    'alignment' => [
			        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
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
			    'font' => [
			        'bold' => true,
			    ],
			    'alignment' => [
			        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			    ],
			];

			// Tabelle anlegen und füllen
			$spreadsheet->createSheet();
			$spreadsheet->setActiveSheetIndex(0);
			$spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
			$spreadsheet->getActiveSheet()->getStyle('A3:D3'.$recordCount)->applyFromArray($styleArray2); // Zeilen mit Datensätzen formatieren
			$spreadsheet->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray); // Um Markierung zurückzusetzen
			$spreadsheet->getActiveSheet()->mergeCells('A1:K1');
			$spreadsheet->getActiveSheet()->setTitle('Statistik')
			            ->setCellValue('A1', 'BdF-Mitgliederstatistik Stichtag: '.\Input::post('stichtag'))
			            ->setCellValue('A3', 'Altersbereich')
			            ->setCellValue('B3', 'Alle')
			            ->setCellValue('C3', 'Männlich')
			            ->setCellValue('D3', 'Weiblich');

			// Daten schreiben
			$zeile = 4;
			$summe = array('alle' => 0, 'm' => 0, 'w' => 0);
			foreach($statistik as $item)
			{
				$spreadsheet->getActiveSheet()
				            ->setCellValue('A'.$zeile, $item['from'].'-'.$item['to'])
				            ->setCellValue('B'.$zeile, $item['alle'])
				            ->setCellValue('C'.$zeile, $item['m'])
				            ->setCellValue('D'.$zeile, $item['w']);
				$summe['alle'] += $item['alle'];
				$summe['m'] += $item['m'];
				$summe['w'] += $item['w'];
				$zeile++;
			}
			$spreadsheet->getActiveSheet()
			            ->setCellValue('A'.$zeile, 'Summe')
			            ->setCellValue('B'.$zeile, $summe['alle'])
			            ->setCellValue('C'.$zeile, $summe['m'])
			            ->setCellValue('D'.$zeile, $summe['w']);

			// Spaltenbreite automatisch einstellen
			foreach($spreadsheet->getActiveSheet()->getColumnIterator() as $column)
			{
				$spreadsheet->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
			}

			//$spreadsheet->getActiveSheet()->freezePane('F2'); // Bereich einfrieren/fixieren
			$spreadsheet->setActiveSheetIndex(0);

			// Überflüssiges Tabellenblatt 'Worksheet 1' löschen
			$sheetIndex = $spreadsheet->getIndex(
			    $spreadsheet->getSheetByName('Worksheet 1')
			);
			$spreadsheet->removeSheetByIndex($sheetIndex);

			// Dateiname festlegen
			$datum = substr(\Input::post('stichtag'), 6, 4).substr(\Input::post('stichtag'), 3, 2).substr(\Input::post('stichtag'), 0, 2);
			$dateiname = 'BdF-Mitgliederstatistik_'.$datum.'_erstellt_am_'.date('Ymd-Hi').'.xls';

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


			$dauer = sprintf('%f0.4', microtime(true) - $start);
			//\System::log('Spielerimport aus Datei '.$objFile->name.' - '.($neu_count+$update_count).' Datensätze - '.$neu_count.' neu, '.$update_count.' überschrieben - Dauer: '.$dauer.'s', __METHOD__, TL_GENERAL);

			// Cookie setzen und zurückkehren zur Adressenliste (key=import aus URL entfernen)
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			//$this->redirect(str_replace('&key=statistik', '', \Environment::get('request')));
		}

		// Formularfelder generieren
		$liste = '<select name="altersstruktur">';
		$objListe = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_mitgliederstatistik WHERE published=?")
		                                    ->execute(1);
		$arrPlayers = array();
		if($objListe->numRows)
		{
			while($objListe->next())
			{
				$struktur = unserialize($objListe->ageperiods);
				$temp = '';
				if(is_array($struktur))
				{
					foreach($struktur as $item)
					{
						$temp .= $item['from'].'-'.$item['to'].', ';
					}
					$titel = ' ('.substr($temp, 0, -2).')';
				}
				$liste .= '<option value="'.$objListe->id.'">'.$objListe->title.$titel.'</option>';
			}
		}
		$liste .= '</select>';

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=statistik', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_fernschach_mitgliederstatistik" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">

<div class="tl_formbody_edit">
	<input type="hidden" name="FORM_SUBMIT" value="tl_fernschach_mitgliederstatistik">
	<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

	<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['headline'].'</h2>
	<div class="tl_tbox">
		<div class="widget">
			<h3>'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['stichtag'][0].'</h3>
			<input type="text" name="stichtag" value="'.(\Input::post('stichtag') ? \Input::post('stichtag') : date('d.m.Y')).'">
			<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['stichtag'][1].'</p>
		</div>
		<div class="widget">
			<h3>'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['altersstruktur'][0].'</h3>
			'.$liste.'
			<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['altersstruktur'][1].'</p>
		</div>
		<div class="widget">
			<h3>'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['aktiviert'][0].'</h3>
			<input type="checkbox" name="aktiviert" checked>
			<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['aktiviert'][1].'</p>
		</div>
	</div>
</div>


<div class="tl_formbody_submit">

<div class="tl_submit_container">
	<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['start']).'">
</div>

</div>
</form>
</div>';

	}


	public function getStatistik($stichtag, $altersstrukturId)
	{
		// Gewünschte Altersstruktur laden
		$objStruktur = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_mitgliederstatistik WHERE id = ?")
		                                       ->execute($altersstrukturId);
		$altersstruktur = array();
		if($objStruktur->numRows)
		{
			$altersstruktur = unserialize($objStruktur->ageperiods);
			// Geschlechter hinzufügen
			for($x = 0; $x < count($altersstruktur); $x++)
			{
				$altersstruktur[$x]['alle'] = 0;
				$altersstruktur[$x]['w'] = 0;
				$altersstruktur[$x]['m'] = 0;
			}
		}

		// Stichtag umwandeln
		if($stichtag)
		{
			$datum = substr($stichtag, 6, 4).substr($stichtag, 3, 2).substr($stichtag, 0, 2);
		}
		else
		{
			$datum = date('Ymd');
		}

		// Mitgliederdatenbank durchsuchen
		if(\Input::post('aktiviert'))
		{
			$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE published=?")
			                                     ->execute(1);
		}
		else
		{
			$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler")
			                                     ->execute();
		}

		if($objPlayer->numRows)
		{
			$anzahl = 0;
			while($objPlayer->next())
			{
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer, $datum);
				if($mitglied)
				{
					$anzahl++;
					$alter = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getAlter($objPlayer->birthday, $datum);
					if($alter == 0)
					{
						\System::log('BdF-Mitgliederstatistik: '.$objPlayer->nachname.','.$objPlayer->vorname.' ohne Geburtstag -> nicht mitgezählt', __METHOD__, TL_GENERAL);
					}
					for($x = 0; $x < count($altersstruktur); $x++)
					{
						if($alter >= $altersstruktur[$x]['from'] && $alter <= $altersstruktur[$x]['to'])
						{
							if($objPlayer->sex == 'w')
							{
								$altersstruktur[$x]['w']++;
							}
							elseif($objPlayer->sex == 'm')
							{
								$altersstruktur[$x]['m']++;
							}
							else
							{
								$altersstruktur[$x]['m']++;
								\System::log('BdF-Mitgliederstatistik: '.$objPlayer->nachname.','.$objPlayer->vorname.' ohne Geschlecht -> als männlich gezählt', __METHOD__, TL_GENERAL);
							}
							$altersstruktur[$x]['alle']++;
						}
					}
					//print_r($alter);
					//echo "<br>";
				}
			}
		}

		$statistik = $altersstruktur;
		return $statistik;
	}


}
