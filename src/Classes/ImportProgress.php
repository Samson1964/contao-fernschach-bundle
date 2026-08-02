<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;

/**
 * Zeigt den Fortschritt des ICCF-Wertungslistenimports an.
 */
class ImportProgress extends Backend
{

	/**
	 * Baut die Fortschrittsseite des Imports.
	 *
	 * Die Seite selbst importiert nichts — sie gibt nur die Gesamtzahl der
	 * Zeilen aus und bindet import_iccf.js ein. Das Skript ruft anschließend
	 * blockweise die Route /contao/fernschach/iccf-import auf (siehe
	 * Controller\IccfImportController).
	 *
	 * @param DataContainer $dc Der aufrufende Data Container. Wird nicht
	 *                          ausgewertet, gehört aber zur Signatur, mit der
	 *                          Contao die in der config.php eingetragenen
	 *                          Backend-Aktionen aufruft
	 *
	 * @return string Der HTML-Code der Seite. Leer, wenn die Aktion gar nicht
	 *                aufgerufen wurde oder in der Sitzung keine Importdaten
	 *                stehen — Letzteres passiert, wenn die Sitzung zwischen
	 *                Hochladen und Import abgelaufen ist
	 */
	public function importProgress(DataContainer $dc)
	{
		if(Input::get('key') != 'importProgress')
		{
			return '';
		}

		// Die Angaben stammen aus ImportRating::importCSV. Der frühere
		// Container-Dienst "session" existiert seit Symfony 6 nicht mehr.
		$daten = Scope::getSessionValue('iccf_import');

		if(!is_array($daten) || !isset($daten['zeilen']))
		{
			Message::addError('Es liegen keine Importdaten vor. Bitte laden Sie die Datei erneut hoch.');

			return Message::generate();
		}

		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaofernschach/js/import_iccf.js';

		$html = '
<script>const anzahlZeilen = '.(int) $daten['zeilen'].';</script>
<div class="content">
<div id="tl_buttons">
<a href="'.StringUtil::ampersand(str_replace('&key=importProgress', '', Environment::get('request'))).'" class="header_back" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
<div id="tl_listing" class="tl_listing_container">
<div class="tl_content_header" id="progressheader">Import wird initialisiert</div>
<div class="tl_content">
<div id="progressbar"><div><span>0%</span></div></div>
<div id="progresstext">0 / '.(int) $daten['zeilen'].'</div>
</div>
</div>
</div>';

		return $html;
	}
}
