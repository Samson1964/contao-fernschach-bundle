<?php
/*
 * =============================================================================
 * FernschachBot
 * =============================================================================
 */

/**
 * Contao Open Source CMS, Copyright (C) 2005-2025 Leo Feyer
 */

use Contao\Controller;

/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
define('TL_SCRIPT', 'bundles/contaofernschach/FernschachBot.php');
require($_SERVER['DOCUMENT_ROOT'].'/../system/initialize.php'); 

class FernschachBot
{
	public function run()
	{
	}
}

/**
 * Instantiate controller
 */
$objFernschachBot = new FernschachBot();
$objFernschachBot->run();
