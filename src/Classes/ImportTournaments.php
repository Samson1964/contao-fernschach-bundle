<?php

namespace Schachbulle\ContaoMitgliederverwaltungBundle\Classes;

/**
 * Class ImportTournaments
  */
class ImportTournaments extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Importiert eine Turnierliste
	 */
	public function run()
	{

		if(\Input::get('key') != 'importTournaments')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Formular wurde abgeschickt, Wortliste importieren
		if (\Input::post('FORM_SUBMIT') == 'tl_mitgliederverwaltung_import')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach ($arrUploaded as $txtFile)
			{
				$objFile = new \File($txtFile, true);

				if ($objFile->extension != 'csv')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				log_message('Importiere Datei: '.$txtFile,'mitgliederverwaltung.log');
				$resFile = $objFile->handle;
				$record_count = 0;
				$neu_count = 0;
				$update_count = 0;
				$kopf = array(); // Nimmt die Spaltennamen aus Zeile 1 auf
				$start = microtime(true);

				while(!feof($resFile))
				{
					$zeile = trim(fgets($resFile));
					$spalte = explode(';', $zeile);
					if($record_count == 0)
					{
						// Kopfzeile auslesen
						$kopf = $spalte;
						log_message('Lese Kopfzeile '.$record_count.': '.$zeile,'mitgliederverwaltung.log');
					}
					else
					{
						log_message('Importiere Datenzeile '.$record_count.': '.$zeile,'mitgliederverwaltung.log');
						// Datensatz auslesen
						$set = array();
						$mitgliedsdaten = array();
						for($x = 0; $x < count($spalte); $x++)
						{
							switch($kopf[$x])
							{
								case 'titel':
									$set['titel'] = $spalte[$x]; break;
								case 'kennziffer':
									$set['kennziffer'] = $spalte[$x]; break;
								case 'registrationDate':
									$set['registrationDate'] = strtotime(str_replace('.', '-', $spalte[$x])); break;
								case 'startDate':
									$set['startDate'] = strtotime(str_replace('.', '-', $spalte[$x])); break;
								case 'art':
									$set['art'] = $spalte[$x]; break;
								case 'nenngeld':
									$set['nenngeld'] = (double)str_replace(',', '.', $spalte[$x]); break;
								case 'turnierleiterName':
									$set['turnierleiterName'] = $spalte[$x]; break;
								case 'turnierleiterEmail':
									$set['turnierleiterEmail'] = $spalte[$x]; break;
								case 'published':
									$set['published'] = $spalte[$x]; break;
								default:
							}
						}

						if($set['titel'])
						{
							// Nach Titel suchen
							$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_mitgliederverwaltung_tournaments WHERE titel = ?")
							                                     ->limit(1)
							                                     ->execute($set['titel']);
						}

						if($objResult->numRows)
						{
							log_message('Set-Array Update:','mitgliederverwaltung.log');
							log_message(print_r($set,true),'mitgliederverwaltung.log');
							// Turniertitel bereits vorhanden, dann überschreiben
							$objUpdate = \Database::getInstance()->prepare("UPDATE tl_mitgliederverwaltung_tournaments %s WHERE id = ?")
							                                     ->set($set)
							                                     ->execute($objResult->id);
							\Controller::createNewVersion('tl_mitgliederverwaltung_tournaments', $objResult->id);
							$update_count++;
						}
						else
						{
							if($set)
							{
								log_message('Set-Array Insert:','mitgliederverwaltung.log');
								log_message(print_r($set,true),'mitgliederverwaltung.log');
								// Neues Turnier
								$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_mitgliederverwaltung_tournaments %s")
								                                     ->set($set)
								                                     ->execute();
								$neu_count++;
							}
						}
					}
					$record_count++;
				}

				$dauer = sprintf('%f0.4', microtime(true) - $start);
				\System::log('Turnierimport aus Datei '.$objFile->name.' - '.($neu_count+$update_count).' Datensätze - '.$neu_count.' neu, '.$update_count.' überschrieben - Dauer: '.$dauer.'s', __METHOD__, TL_GENERAL);
			}

			// Cookie setzen und zurückkehren zur Adressenliste (key=import aus URL entfernen)
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importTournaments', '', \Environment::get('request')));
		}

		// Return form
		return '
<div class="content">
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importTournaments', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_importTournaments']['headline'].'</h2>
<p style="margin: 18px;">'.$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_importTournaments']['format'].'</div>
'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_wortliste_import" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_mitgliederverwaltung_import">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

<div class="widget">
  <h3>'.$GLOBALS['TL_LANG']['MSC']['source'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MSC']['source'][1]) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MSC']['source'][1].'</p>' : '').'
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['tw_import'][0]).'">
</div>

</div>
</form>
</div>';

	}

	public function is_utf8($str)
	{
	    $strlen = strlen($str);
	    for ($i = 0; $i < $strlen; $i++) {
	        $ord = ord($str[$i]);
	        if ($ord < 0x80) continue; // 0bbbbbbb
	        elseif (($ord & 0xE0) === 0xC0 && $ord > 0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
	        elseif (($ord & 0xF0) === 0xE0) $n = 2; // 1110bbbb
	        elseif (($ord & 0xF8) === 0xF0 && $ord < 0xF5) $n = 3; // 11110bbb (exkl F5-FF)
	        else return false; // ungültiges UTF-8-Zeichen
	        for ($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
	            if (++$i === $strlen || (ord($str[$i]) & 0xC0) !== 0x80)
	                return false; // ungültiges UTF-8-Zeichen
	    }
	    return true; // kein ungültiges UTF-8-Zeichen gefunden
	}

}
