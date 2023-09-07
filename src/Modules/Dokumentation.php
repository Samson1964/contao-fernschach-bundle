<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Modules;

/**
 * Back end module "maintenance".
 */
class Dokumentation extends \BackendModule
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_maintenance';

	/**
	 * Generate the module
	 *
	 * @throws \Exception
	 */
	protected function compile()
	{
		\System::loadLanguageFile('tl_fernschach_dokumentation');

		$this->Template->content = '<div class="fernschach-doku">';
		$this->Template->href = $this->getReferer(true);
		$this->Template->title = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];

		$this->Template->content .= '<h1>'.$GLOBALS['TL_LANG']['tl_fernschach_dokumentation']['title'].'</h1>';
		$this->Template->content .= $GLOBALS['TL_LANG']['tl_fernschach_dokumentation']['text'];

		// Serienmails
		$this->Template->content .= '<h2>'.$GLOBALS['TL_LANG']['tl_fernschach_dokumentation']['serienmail_title'].'</h2>';
		$this->Template->content .= $GLOBALS['TL_LANG']['tl_fernschach_dokumentation']['serienmail_text'];

		$this->Template->content .= '</div>';

	}
}
