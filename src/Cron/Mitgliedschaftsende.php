<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;

/**
 * Beendet abgelaufene Mitgliedschaften.
 *
 * Einmal täglich wird geprüft, ob eine Mitgliedschaft zum Vortag ausgelaufen
 * ist, und der Spieler entsprechend aus der Mitgliedschaft genommen.
 */
class Mitgliedschaftsende
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
	 * Das Intervall ("daily") steht in src/Resources/config/services.yaml und
	 * nicht mehr in einer Annotation: Contao wertet Annotationen und
	 * Attribute nur bei Diensten mit autoconfigure aus.
	 *
	 * @return void
	 */
	public function onDaily(): void
	{
		// Ohne initialisiertes Framework gibt es keine Contao-Datenbankverbindung.
		$this->framework->initialize();

		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschaftsende-Prüfung gestartet');

		// ===================================================================================
		// Überprüft veröffentlichte und nichtarchivierte Spieler, ob sie noch Mitglied sind
		// Falls ja, wird der Spieler archiviert
		// ===================================================================================
		$objPlayer = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ? AND published = ?")
		                                     ->execute('', 1);

		if($objPlayer->numRows)
		{
			while($objPlayer->next())
			{
				// Ist der Spieler noch Mitglied im BdF?
				$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer, NULL, $objPlayer->published);
				if(!$mitglied)
				{
					// Archivierung des Spielers notwendig, da nicht mehr Mitglied
					$set = array
					(
						'tstamp'   => time(),
						'archived' => 1,
					);
					Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
					                        ->set($set)
					                        ->execute($objPlayer->id);
					// Log-Eintrag vornehmen
					System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] - Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (BdF-Mitgliedsnummer '.$objPlayer->memberId.') archiviert, da kein Mitglied.');
				}
			}
		}

		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitgliedschaftsende-Prüfung beendet');
		
	}

}
