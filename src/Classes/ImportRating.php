<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\DataContainer;
use Contao\Database;
use Contao\DropZone;
use Contao\Environment;
use Contao\File;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;

/**
 * Nimmt eine ICCF-Wertungsliste als CSV-Datei entgegen.
 */
class ImportRating extends Backend
{

	/**
	 * Zeigt das Hochladeformular und nimmt die Datei entgegen.
	 *
	 * Eingelesen wird hier noch nichts: Die Datei wird nur nach system/tmp
	 * gelegt, ihre Zeilenzahl bestimmt und zusammen mit der Ziel-Wertungsliste
	 * in der Sitzung hinterlegt. Anschließend geht es weiter zur
	 * Fortschrittsseite (siehe ImportProgress), die den eigentlichen Import
	 * blockweise anstößt. Alle bisherigen Wertungen dieser Liste werden vorher
	 * auf "nicht veröffentlicht" gesetzt, damit nach dem Import nur noch das
	 * steht, was wirklich in der neuen Datei vorkam.
	 *
	 * @param DataContainer $dc Der aufrufende Data Container. Wird nicht
	 *                          ausgewertet, gehört aber zur Signatur, mit der
	 *                          Contao die Backend-Aktionen aufruft
	 *
	 * @return string Das Formular als HTML. Leer, wenn die Aktion nicht
	 *                aufgerufen wurde. Nach erfolgreichem Hochladen kehrt die
	 *                Methode nicht zurück, sondern leitet weiter
	 */
	public function importCSV(DataContainer $dc)
	{
		if(Input::get('key') != 'importCSV')
		{
			return '';
		}

		$this->import(BackendUser::class, 'User');

		// Contao merkt sich am Benutzer, welchen Datei-Uploader er benutzt. Dort
		// steht ein Klassenname ohne Namensraum ("DropZone"), den es als
		// globalen Alias nur bis Contao 4.13 gab. Fällt die Prüfung deshalb
		// negativ aus, greift derselbe Standard wie im Contao-Kern.
		$class = $this->User->uploader;

		if (!$class || !class_exists($class))
		{
			$class = DropZone::class;
		}

		$objUploader = new $class();

		// Importiere die Daten, wenn das Formular abgeschickt wurde
		if(Input::post('FORM_SUBMIT') == 'tl_fernschach_iccf_import')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			// Die zuletzt hochgeladene CSV-Datei gewinnt. $objFile wird unterhalb
			// der Schleife weiterbenutzt und muss deshalb auch dann gesetzt sein,
			// wenn gar keine passende Datei dabei war — bis Version 2.1.0 lief
			// der Ablauf in diesem Fall in einen Fatal Error.
			$objFile = null;

			foreach($arrUploaded as $strFile)
			{
				$objDatei = new File($strFile);

				if($objDatei->extension != 'csv')
				{
					Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objDatei->extension));
					continue;
				}

				$objFile = $objDatei;
			}

			if(null === $objFile)
			{
				Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'] ?? 'Die Datei ist keine CSV-Datei (%s).', 'csv'));
				$this->reload();
			}

			// Einstellungen der Ratingliste laden
			$objListe = Database::getInstance()->prepare('SELECT * FROM tl_fernschach_iccf_ratinglists WHERE id = ?')
			                                    ->execute(Input::get('id'));

			// Zeilenanzahl ermitteln
			$zeilen = file($objFile->dirname.'/'.$objFile->basename);
			$anzahlZeilen = count($zeilen);

			// Importdaten in der Sitzung ablegen. Von dort holt sie sich der
			// IccfImportController bei jedem der folgenden Teilaufrufe. Der
			// frühere Container-Dienst "session" existiert seit Symfony 6 nicht
			// mehr, deshalb der Umweg über die Anfrage (siehe Scope).
			$daten = array
			(
				'pfad'     => $objFile->dirname,
				'datei'    => $objFile->basename,
				'zeilen'   => $anzahlZeilen,
				'listDate' => $objListe->fromDate,
				'listId'   => Input::get('id'),
			);

			if(!Scope::setSessionValue('iccf_import', $daten))
			{
				Message::addError('Die Importdaten konnten nicht in der Sitzung abgelegt werden.');
				$this->reload();
			}

			// Alte Datensätze auf unveröffentlicht setzen
			Database::getInstance()->prepare('UPDATE tl_fernschach_iccf_ratings SET published = ? WHERE listId = ?')
			                        ->execute('', Input::get('id'));

			System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importCSV', '&key=importProgress', Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.StringUtil::ampersand(str_replace('&key=importCSV', '', Environment::get('request'))).'" class="header_back" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

'.Message::generate().'
<form action="'.StringUtil::ampersand(Environment::get('request'), true).'" id="tl_fernschach_iccf_import" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">

<div class="tl_formbody_edit">
	<input type="hidden" name="FORM_SUBMIT" value="tl_fernschach_iccf_import">
	<input type="hidden" name="REQUEST_TOKEN" value="'.Scope::getRequestToken().'">
	<input type="hidden" name="MAX_FILE_SIZE" value="' . Config::get('maxFileSize') . '">

	<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_fernschach_iccf_import']['headline'].'</h2>
	<p style="margin: 18px;">'.$GLOBALS['TL_LANG']['tl_fernschach_iccf_import']['format'].'

	<div class="widget">
		<h3>'.$GLOBALS['TL_LANG']['MOD']['iccf_import_file'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MOD']['iccf_import'][1]) ? '
		<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MOD']['iccf_import_file'][1].'</p>' : '').'
	</div>
</div>

<div class="tl_formbody_submit">

	<div class="tl_submit_container">
		<input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['tw_import'][0]).'">
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
