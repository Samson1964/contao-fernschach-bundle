<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\CronJob;

/**
 * Provide methods to run automated jobs.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class Mitgliedschaftsende
{
	private ContaoFramework $framework;

	/**
	 * Make the constuctor public
	 */
	public function __construct(ContaoFramework $framework)
	{
	}

	/**
	 * @CronJob("daily")
	 */
	public function onDaily(): void
	{
		// Log-Eintrag vornehmen
		\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschaftsende-Prüfung gestartet');

		// ===================================================================================
		// Überprüft veröffentlichte und nichtarchivierte Spieler, ob sie noch Mitglied sind
		// Falls ja, wird der Spieler archiviert
		// ===================================================================================
		$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ? AND published = ?")
		                                     ->execute('', 1);

		if($objPlayer->numRows)
		{
			while($objPlayer->next())
			{
				// Ist der Spieler noch Mitglied im BdF?
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer->memberships, NULL, $objPlayer->published);
				if(!$mitglied)
				{
					// Archivierung des Spielers notwendig, da nicht mehr Mitglied
					$set = array
					(
						'tstamp'   => time(),
						'archived' => 1,
					);
					\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
					                        ->set($set)
					                        ->execute($objPlayer->id);
					// Log-Eintrag vornehmen
					\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] - Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (BdF-Mitgliedsnummer '.$objPlayer->memberId.') archiviert, da kein Mitglied.');
				}
			}
		}

		// Log-Eintrag vornehmen
		\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschaftsende-Prüfung beendet');
		
	}

}
