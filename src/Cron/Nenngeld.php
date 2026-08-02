<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;

/**
 * Überwacht die Nenngeldkonten.
 *
 * Stündlich werden die Nenngeldkonten auf negative Salden geprüft; die
 * Ergebnisse gehen als Bericht an die in den Einstellungen hinterlegte
 * Adresse.
 */
class Nenngeld
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

		// Nenngeldprüfung ausführen
		$nenngeldpruefung = 'Nenngeldkonten-Prüfung fehlgeschlagen';
		//System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Zeitmessung startet');
		//$time_start = microtime(true); 
		$ergebnis = \Schachbulle\ContaoFernschachBundle\Classes\Konto\Nenngeld::getNegativ();
		//$time_end = microtime(true);
		//$time = $time_end - $time_start;
		//System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Dauer getNegativ in Sekunden: '.$time);
		if(isset($ergebnis))
		{
			$nenngeldpruefung = 'Nenngeldkonto negativ: <span style="color:red;">'.$ergebnis['summe_alle'].' € bei '.$ergebnis['anzahl_alle'].' veröffentlichten Spielern</span>';
			$nenngeldpruefung .= ' / davon <span style="color:red;">'.$ergebnis['summe_mitglieder'].' € bei '.$ergebnis['anzahl_mitglieder'].' Mitgliedern</span>';
			$nenngeldpruefung .= ' <span style="color:#575757;"><i>(Letzte Prüfung: '.date('d.m.Y H:i').')</i></span>';
		}
		
		$file = System::getContainer()->getParameter('kernel.project_dir').'/system/tmp/contao-fernschach-bundle_nenngeld.txt';
		file_put_contents($file, $nenngeldpruefung);

		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Nenngeld-Konten wurden überprüft');
		
	}

}
