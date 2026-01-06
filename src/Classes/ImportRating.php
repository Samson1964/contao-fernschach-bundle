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
		if(\Input::get('key') != 'importCSV')
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

			foreach($arrUploaded as $strFile)
			{
				$objFile = new \File($strFile, true);

				if($objFile->extension != 'csv')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				$resFile = $objFile->handle;
			}

			// Einstellungen der Ratingliste laden
			$objListe = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_iccf_ratinglists WHERE id = ?')
			                                    ->execute(\Input::get('id'));

			// Zeilenanzahl ermitteln
			$zeilen = file($objFile->dirname.'/'.$objFile->basename);
			$anzahlZeilen = count($zeilen);
			// Sitzung laden und Importdaten initialisieren
			$session = \System::getContainer()->get('session');
			$daten = array
			(
				'pfad'     => $objFile->dirname,
				'datei'    => $objFile->basename,
				'zeilen'   => $anzahlZeilen,
				'listDate' => $objListe->fromDate,
				'listId'   => \Input::get('id'),
			);
			$session->set('iccf_import', $daten);

			// Alte Datensätze auf unveröffentlicht setzen
			\Database::getInstance()->prepare('UPDATE tl_fernschach_iccf_ratings SET published = ? WHERE listId = ?')
			                        ->execute('', \Input::get('id'));

			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importCSV', '&key=importProgress', \Environment::get('request')));
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
