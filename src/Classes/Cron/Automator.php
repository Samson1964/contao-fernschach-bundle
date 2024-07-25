<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Cron;

/**
 * Provide methods to run automated jobs.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class Automator extends \System
{

	/**
	 * Make the constuctor public
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Check for new \Contao versions
	 */
	public function checkForUpdates()
	{
		//if (!is_numeric(BUILD))
		//{
		//	return;
		//}
        //
		//// HOOK: proxy module
		//if (Config::get('useProxy')) {
		//	$objRequest = new \ProxyRequest();
		//} else {
		//	$objRequest = new \Request();
		//}
        //
		//$objRequest->send(\Config::get('liveUpdateBase') . (LONG_TERM_SUPPORT ? 'lts-version.txt' : 'version.txt'));
        //
		//if (!$objRequest->hasError())
		//{
		//	\Config::set('latestVersion', $objRequest->response);
		//	\Config::persist('latestVersion', $objRequest->response);
		//}

		// Add a log entry
		
		//\System::getContainer()->get('monolog.logger.contao.cron')->info('Fernschach-Verwaltung');
	}

}
