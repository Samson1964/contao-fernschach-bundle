<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\System;
use Schachbulle\ContaoFernschachBundle\Classes\Todesfall;

/**
 * Beendet die Mitgliedschaft verstorbener Spieler.
 *
 * Einmal täglich wird geprüft, ob bei einem als verstorben gekennzeichneten
 * Spieler mit Todestag noch eine offene Mitgliedschaft steht. Ist das so, wird
 * der Todestag als Ende eingetragen — ohne diesen Schritt gilt der Spieler
 * weiterhin als Mitglied, und der Beitrag läuft weiter.
 *
 * Ein Todesvermerk ohne Todestag lässt sich hier nicht heilen: Das Datum kennt
 * nur ein Mensch. Solche Datensätze werden deshalb nur protokolliert; im
 * Backend-Modul *Spieler* listet sie die Schaltfläche „Verstorbene prüfen" auf.
 */
class Todesfallpruefung
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
	 * Wird täglich vom Contao-Cron aufgerufen.
	 *
	 * Das Intervall ("daily") steht in src/Resources/config/services.yaml und
	 * nicht in einer Annotation: Contao wertet Annotationen und Attribute nur
	 * bei Diensten mit autoconfigure aus.
	 *
	 * @return void
	 */
	public function onDaily(): void
	{
		// Ohne initialisiertes Framework gibt es keine Contao-Datenbankverbindung.
		$this->framework->initialize();

		$objLog = System::getContainer()->get('monolog.logger.contao.cron');

		// ===================================================================================
		// Verstorbene mit Todestag: laufende Mitgliedschaft mit dem Todestag beenden
		// ===================================================================================
		$objSpieler = Database::getInstance()->prepare('SELECT id, nachname, vorname, memberId, deathday, memberships FROM tl_fernschach_spieler WHERE death = ? AND deathday > ?')
		                                      ->execute(1, 0);

		while ($objSpieler->next())
		{
			$arrMitgliedschaften = Todesfall::beendeMitgliedschaft($objSpieler->memberships, $objSpieler->deathday);

			if (null === $arrMitgliedschaften)
			{
				// Die Mitgliedschaft ist bereits beendet — hier ist nichts zu tun
				continue;
			}

			Database::getInstance()->prepare('UPDATE tl_fernschach_spieler %s WHERE id = ?')
			                        ->set(array('tstamp' => time(), 'memberships' => serialize($arrMitgliedschaften)))
			                        ->execute($objSpieler->id);

			$objLog->info('[Fernschach-Wartung] Spieler '.$objSpieler->nachname.','.$objSpieler->vorname.' (ID '.$objSpieler->id.') ist verstorben ('.$objSpieler->deathday.') &#10142; Mitgliedschaft beendet');
		}

		// ===================================================================================
		// Verstorbene ohne Todestag: nur melden, reparieren kann das keine Routine
		// ===================================================================================
		$arrOhneDatum = Todesfall::ohneTodestag();

		if ($arrOhneDatum)
		{
			// Eine Zeile für alle: Der Bestand kann groß sein, und die Meldung
			// wiederholt sich jeden Tag, solange niemand die Daten nachträgt.
			// Je Spieler eine Zeile würde das Protokoll zuschütten.
			$arrNamen = array();

			foreach ($arrOhneDatum as $arrEintrag)
			{
				$arrNamen[] = $arrEintrag['name'].' (ID '.$arrEintrag['id'].')';
			}

			$strNamen = \count($arrNamen) > 10
				? implode(', ', \array_slice($arrNamen, 0, 10)).' und '.(\count($arrNamen) - 10).' weitere'
				: implode(', ', $arrNamen);

			$objLog->info('[Fernschach-Wartung] '.\count($arrOhneDatum).' Spieler sind als verstorben gekennzeichnet, haben aber keinen Todestag &#10142; Mitgliedschaft bleibt offen: '.$strNamen);
		}
	}
}
