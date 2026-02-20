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
class Mitgliedschaftscheck
{
	private ContaoFramework $framework;

	/**
	 * Make the constuctor public
	 */
	public function __construct(ContaoFramework $framework)
	{
	}

	/**
	 * @CronJob("hourly")
	 */
	public function onHourly(): void
	{
		// Log-Eintrag vornehmen
		\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschafts-Check gestartet');

		// ===================================================================================
		// Überprüft alle Spieler auf aktive Mitgliedschaft
		// Entsprechend wird das feld tl_fernschach_spieler.member auf true/false gesetzt
		// ===================================================================================
		$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler")
		                                     ->execute();

		if($objPlayer->numRows)
		{
			while($objPlayer->next())
			{
				// Ist der Spieler noch Mitglied im BdF?
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer);
				if(($mitglied && !$objPlayer->member) || (!$mitglied && $objPlayer->member))
				{
					// member-Feld muß geändert werden, weil Mitgliedschaft nicht paßt
					$set = array
					(
						'tstamp'   => time(),
						'member'   => $mitglied ? true : false
					);
					\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
					                        ->set($set)
					                        ->execute($objPlayer->id);
					$feldMember = $mitglied ? 'ja (Mitglied)' : 'nein (kein Mitglied)';
					// Log-Eintrag vornehmen
					\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] - Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (BdF-Mitgliedsnummer '.$objPlayer->memberId.') Feld member geändert auf: '.$feldMember);
				}
			}
		}

		// Log-Eintrag vornehmen
		\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschafts-Check beendet');
		
	}

}
