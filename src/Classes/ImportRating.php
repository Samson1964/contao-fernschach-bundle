<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class Import
  */
class ImportRating extends \Backend
{

	/**
	 * Return a form to choose a CSV file and import it
	 * @param object
	 * @return string
	 */
	public function importCSV(\DataContainer $dc)
	{
		if (\Input::get('key') != 'importCSV')
		{
			return '';
		}

		$this->import('BackendUser', 'User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Importiere die Daten, wenn das Formular abgeschickt wurde
		if(\Input::post('FORM_SUBMIT') == 'tl_fernschach_iccf_import')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach($arrUploaded as $strFile)
			{
				$objFile = new \File($strFile, true);

				if ($objFile->extension != 'csv')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				$resFile = $objFile->handle;
				$count_import = 0;
				$count_player = 0;
				$start = microtime(true);

				// Alte Datensätze löschen
				\Database::getInstance()->prepare('DELETE FROM tl_fernschach_iccf_ratings WHERE listId = ?')
				                        ->execute(\Input::get('id'));

				while(!feof($resFile))
				{
					$zeile = self::remove_utf8_bom(trim(fgets($resFile)));
					$spalte = explode(';', $zeile);
					$spielername = explode(',', $spalte[3]);
					$count_import++;
					// Nach Spieler mit ICCF-ID (aus Spalte 1) suchen
					$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_iccf_players WHERE iccfid = ?")
					                                     ->execute($spalte[0]);
					if($objPlayer->numRows)
					{
						// ICCF-ID gefunden
						$playerId = $objPlayer->id;
					}
					else
					{
						// ICCF-ID nicht gefunden, Spieler neu eintragen
						$set_player = array
						(
							'tstamp'      => time(),
							'iccfid'      => $spalte[0], // ICCF-ID in Spalte 1
							'country'     => $spalte[1], // Land in Spalte 2
							'surname'     => trim($spielername[0]), // Nachname
							'prename'     => isset($spielername[1]) ? trim($spielername[1]) : '', // Vorname
							'intern'      => NULL,
							'published'   => true
						);
						$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_iccf_players %s")
						                                     ->set($set_player)
						                                     ->execute();
						$playerId = $objInsert->insertId;
						$count_player++;
					}
				}
				$dauer = sprintf('%f0.4', microtime(true) - $start);
				\System::log('ICCF-Import aus Datei '.$objFile->name.' - '.($count_import).' Datensätze im Import - '.$count_player.' Spieler neu, '.$count_player.' Spieler ergänzt - Dauer: '.$dauer.'s', __METHOD__, TL_GENERAL);
			}

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importCSV', '', \Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importCSV', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_fernschach_iccf_import" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">

<div class="tl_formbody_edit">
	<input type="hidden" name="FORM_SUBMIT" value="tl_fernschach_iccf_import">
	<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">
	<input type="hidden" name="MAX_FILE_SIZE" value="' . \Config::get('maxFileSize') . '">

	<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_fernschach_iccf_import']['headline'].'</h2>
	<p style="margin: 18px;">'.$GLOBALS['TL_LANG']['tl_fernschach_iccf_import']['format'].'

	<div class="widget">
		<h3>'.$GLOBALS['TL_LANG']['MOD']['iccf_import_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['iccf_import'][1]) ? '
		<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['iccf_import_file'][1].'</p>' : '').'
	</div>
</div>

<div class="tl_formbody_submit">

	<div class="tl_submit_container">
		<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['tw_import'][0]).'">
	</div>

</div>
</form>
';
	}

	function remove_utf8_bom($text)
	{
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $text);
		return $text;
	}

}
