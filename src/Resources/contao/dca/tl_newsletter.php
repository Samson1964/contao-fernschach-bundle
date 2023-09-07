<?php
// Onload_Callback hinzufügen, um einen Hinweis anzuzeigen
$GLOBALS['TL_DCA']['tl_newsletter']['config']['onload_callback'][] = array('tl_newsletter_fernschach', 'addTemplateWarning');

/**
 * Class tl_newsletter_fernschach
 */
class tl_newsletter_fernschach extends \Backend
{

	/**
	 * Add a warning if there are users with access to the template editor.
	 */
	public function addTemplateWarning()
	{
		if((\Input::get('table') == 'tl_newsletter' || \Input::get('table') == 'tl_newsletter_recipients') && \Input::get('id') == $GLOBALS['TL_CONFIG']['fernschach_newsletter'] && !\Input::get('act'))
		{
			\Message::addInfo($GLOBALS['TL_LANG']['tl_newsletter']['fernschach_hinweis']);
		}

	}
}
