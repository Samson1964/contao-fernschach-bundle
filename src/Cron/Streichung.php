<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\CronJob;

class Streichung
{
	private ContaoFramework $framework;

	/**
	 * @CronJob("daily")
	 */
	public function onDaily(): void
	{

		// ===================================================================================
		// Überprüft die korrekte Setzung der Mitgliedschaftsstreichung
		// ===================================================================================
		$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE published = ?")
		                                     ->execute(1);

		if($objPlayer->numRows)
		{
			while($objPlayer->next())
			{
				if($objPlayer->isDeletion && $objPlayer->streichung == 0)
				{
					// ==========================================================
					// Spieler wurde gestrichen, aber es ist kein Datum angegeben
					// Streichung deshalb jetzt deaktivieren
					// ==========================================================
					$set = array
					(
						'tstamp'     => time(),
						'isDeletion' => false,
					);
					\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
					                        ->set($set)
					                        ->execute($objPlayer->id);
					\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (ID '.$objPlayer->id.') gestrichen, aber ohne Datum &#10142; Streichung deaktiviert');
				}
				elseif(!$objPlayer->isDeletion && $objPlayer->streichung > 0)
				{
					// =======================================================================
					// Spieler hat ein Streichdatum, die Streichung wurde aber nicht aktiviert
					// Streichung deshalb jetzt aktivieren
					// =======================================================================
					$set = array
					(
						'tstamp'     => time(),
						'isDeletion' => true,
					);
					\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
					                        ->set($set)
					                        ->execute($objPlayer->id);
					\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (ID '.$objPlayer->id.') hat ein Streichdatum ('.$objPlayer->streichung.'), wurde aber nicht gestrichen &#10142; Streichung aktiviert');
				}
				elseif($objPlayer->isDeletion && $objPlayer->streichung > 0)
				{
					// =======================================================================
					// Spieler hat ein Streichdatum und die Streichung wurde aktiviert
					// Prüfung ob das Streichdatum in den Mitgliedschaften steht
					// =======================================================================
					$mitgliedschaften = unserialize($objPlayer->memberships);
					$found = false;
					if(is_array($mitgliedschaften))
					{
						for($x = 0; $x < count($mitgliedschaften); $x++)
						{
							if($mitgliedschaften[$x]['to'] == 0)
							{
								// Kein Streichdatum eingetragen, deshalb jetzt eintragen und speichern
								$found = true;
								$mitgliedschaften[$x]['to'] = $objPlayer->streichung;
								$mitgliedschaften[$x]['status'] = 'Streichung';
								$set = array
								(
									'tstamp'      => time(),
									'memberships' => serialize($mitgliedschaften)
								);
								\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objPlayer->id);
								\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (ID '.$objPlayer->id.') hat ein Streichdatum ('.$objPlayer->streichung.'), aber kein Mitgliedschaftsende &#10142; Mitgliedschaft geändert');
							}
							if($mitgliedschaften[$x]['to'] == $objPlayer->streichung)
							{
								// Streichdatum ist bereits eingetragen
								$found = true;
								break;
							}
						}
					}
					if(!$found)
					{
						// Keine passende Mitgliedschaft gefunden, deshalb eine hinzufügen
						$mitgliedschaften[] = array
						(
							'from'      => 0,
							'to'        => $objPlayer->streichung,
							'status'    => 'Streichung'
						);
						$set = array
						(
							'tstamp'      => time(),
							'memberships' => serialize($mitgliedschaften)
						);
						\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler %s WHERE id=?")
						                        ->set($set)
						                        ->execute($objPlayer->id);
						\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Spieler '.$objPlayer->nachname.','.$objPlayer->vorname.' (ID '.$objPlayer->id.') hat ein Streichdatum ('.$objPlayer->streichung.'), aber kein Mitgliedschaftsende &#10142; Mitgliedschaft angelegt');
					}
				}
			}
		}

	}

}
