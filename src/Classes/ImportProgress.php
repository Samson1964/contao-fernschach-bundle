<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class Import
  */
class ImportProgress extends \Backend
{

	/**
	 * Return a form to choose a CSV file and import it
	 * @param object
	 * @return string
	 */
	public function importProgress(\DataContainer $dc)
	{
		if(\Input::get('key') != 'importProgress')
		{
			return '';
		}

		$session = \System::getContainer()->get('session');
		$daten = $session->get('iccf_import');

		$GLOBALS['TL_JAVASCRIPT'][] = '/bundles/contaofernschach/js/import_iccf.js';

		$html = '
<script>const anzahlZeilen = '.$daten['zeilen'].';</script>
<div class="content">
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importProgress', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
<div id="tl_listing" class="tl_listing_container">
<div class="tl_content_header" id="progressheader">Import wird initialisiert</div>
<div class="tl_content">
<div id="progressbar"><div><span>0%</span></div></div>
<div id="progresstext">0 / '.$daten['zeilen'].'</div>
</div>
</div>
</div>';

		return $html;
	}
}
