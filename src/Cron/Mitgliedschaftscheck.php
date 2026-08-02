<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;

/**
 * Hält das Feld "member" der Spieler auf dem aktuellen Stand.
 *
 * Stündlich wird für jeden Spieler geprüft, ob er nach seinen hinterlegten
 * Mitgliedschaftszeiträumen gerade BdF-Mitglied ist, und das Feld entsprechend
 * gesetzt.
 */
class Mitgliedschaftscheck
{
	private ContaoFramework $framework;

	/**
	 * Nimmt das Contao-Framework entgegen.
	 *
	 * @param ContaoFramework $framework Wird gebraucht, damit der Cronjob auch
	 *                                   dann läuft, wenn ihn die Kommandozeile
	 *                                   und nicht eine Anfrage anstößt
	 */
	public function __construct(ContaoFramework $framework)
	{
		$this->framework = $framework;
	}

	/**
	 * Wird stündlich beziehungsweise täglich vom Contao-Cron aufgerufen.
	 *
	 * Das Intervall ("hourly") steht in src/Resources/config/services.yaml und
	 * nicht mehr in einer Annotation: Contao wertet Annotationen und
	 * Attribute nur bei Diensten mit autoconfigure aus.
	 *
	 * @return void
	 */
	public function onHourly(): void
	{
		// Ohne initialisiertes Framework gibt es keine Contao-Datenbankverbindung.
		$this->framework->initialize();

		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschafts-Check gestartet');

		// ===================================================================================
		// Überprüft alle Spieler auf aktive Mitgliedschaft
		// Entsprechend wird das feld tl_fernschach_spieler.member auf true/false gesetzt
		// ===================================================================================
		$objPlayer = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler")
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
					Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
					                        ->set($set)
					                        ->execute($objPlayer->id);
					$feldMember = $mitglied ? 'ja (Mitglied)' : 'nein (kein Mitglied)';
					// Log-Eintrag vornehmen
					System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] - Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (BdF-Mitgliedsnummer '.$objPlayer->memberId.') Feld member geändert auf: '.$feldMember);
				}
			}
		}

		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschafts-Check beendet');
		
	}

}
