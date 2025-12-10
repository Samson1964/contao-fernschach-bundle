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
class Nenngeld
{
	private ContaoFramework $framework;

	/**
	 * Make the constuctor public
	 */
	public function __construct(ContaoFramework $framework)
	{
	}

	/**
	 * @CronJob("minutely")
	 */
	public function onMinutely(): void
	{
		// Nenngeldprüfung ausführen
		$nenngeldpruefung = 'Nenngeldkonten-Prüfung fehlgeschlagen';
		$ergebnis = \Schachbulle\ContaoFernschachBundle\Classes\Konto\Nenngeld::getNegativ();
		if(isset($ergebnis))
		{
			$nenngeldpruefung = 'Nenngeldkonto negativ: <span style="color:red;">'.$ergebnis['summe_alle'].' € bei '.$ergebnis['anzahl_alle'].' veröffentlichten Spielern</span>';
			$nenngeldpruefung .= ' / davon <span style="color:red;">'.$ergebnis['summe_mitglieder'].' € bei '.$ergebnis['anzahl_mitglieder'].' Mitgliedern</span>';
			$nenngeldpruefung .= ' <span style="color:#575757;"><i>(Letzte Prüfung: '.date('d.m.Y H:i').')</i></span>';
		}
		
		$file = TL_ROOT.'/vendor/schachbulle/contao-fernschach-bundle/src/Resources/nenngeld.txt';
		file_put_contents($file, $nenngeldpruefung);

		// Log-Eintrag vornehmen
		\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Nenngeld-Konten wurden überprüft');
		
	}

}
