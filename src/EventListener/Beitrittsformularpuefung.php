<?php

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\EventListener;

use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Form;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

/**
 * Legt aus einem abgeschickten Beitrittsformular einen Spielerdatensatz an.
 *
 * Der Hook ist in src/Resources/contao/config/config.php eingetragen. Das
 * frühere Attribut #[AsHook] stand zusätzlich an dieser Klasse, hat aber nie
 * gegriffen: Contao wertet es nur bei Diensten mit "autoconfigure" aus, und die
 * Klasse ist kein Dienst. Wäre sie einer geworden, hätte der Hook doppelt
 * gefeuert und für jede Beitrittserklärung zwei Spieler angelegt.
 */
class Beitrittsformularpuefung
{
	/**
	 * Wird nach dem Absenden eines beliebigen Contao-Formulars aufgerufen.
	 *
	 * Zugeschlagen wird nur bei dem Formular, das in den Einstellungen als
	 * Beitrittsformular hinterlegt ist. Die bekannten Felder wandern in die
	 * passenden Spalten von tl_fernschach_spieler, alles Weitere sammelt sich
	 * als Fließtext im Feld info_beitritt — dort steht auch der Geburtstag, weil
	 * das Datenbankfeld birthday ein anderes Format erwartet, als das Formular
	 * liefert.
	 *
	 * @param array      $submittedData Die abgeschickten Formularwerte, Feldname => Wert
	 * @param array      $formData      Der Formulardatensatz aus tl_form; ausgewertet
	 *                                  wird nur die ID
	 * @param array|null $files         Hochgeladene Dateien; hier ungenutzt, gehört
	 *                                  aber zur Signatur des Hooks
	 * @param array      $labels        Die Feldbeschriftungen; hier ungenutzt
	 * @param Form       $form          Das Formularobjekt; hier ungenutzt
	 *
	 * @return void Der neue Spieler wird sofort veröffentlicht angelegt und der
	 *              Vorgang im Systemprotokoll vermerkt. Ist in den Einstellungen
	 *              kein Beitrittsformular hinterlegt, passiert nichts
	 */
	public function __invoke(array $submittedData, array $formData, ?array $files, array $labels, Form $form): void
	{
		// Die union-Schreibweise "array|null" im Parameter setzte PHP 8 voraus;
		// "?array" bedeutet dasselbe und läuft auch unter PHP 7.4.
		if(Config::get('fernschach_beitrittsformular'))
		{
			// Ein Beitrittsformular wurde konfiguriert/zugewiesen
			if(Config::get('fernschach_beitrittsformular') == $formData['id'])
			{
				// Das abgesendete Formular ist das Beitrittsformular, Daten übernehmen und Spielerdatensatz anlegen

				// Sonstige Informationen sichern
				$info_beitritt = '';
				if(isset($submittedData['geburtstag'])) $info_beitritt .= 'Geburtstag: '.$submittedData['geburtstag']."\n";
				if(isset($submittedData['staat'])) $info_beitritt .= 'Staat: '.$submittedData['staat']."\n";
				if(isset($submittedData['bdf_mitglied'])) $info_beitritt .= 'BdF-Mitglied: '.$submittedData['bdf_mitglied']."\n";
				if(isset($submittedData['fernschach_erfolge'])) $info_beitritt .= 'Fernschach-Erfolge: '.$submittedData['fernschach_erfolge']."\n";
				if(isset($submittedData['nahschach_erfolge'])) $info_beitritt .= 'Nahschach-Erfolge: '.$submittedData['nahschach_erfolge']."\n";
				if(isset($submittedData['elo'])) $info_beitritt .= 'Elo: '.$submittedData['elo']."\n";
				if(isset($submittedData['dwz'])) $info_beitritt .= 'DWZ: '.$submittedData['dwz']."\n";
				if(isset($submittedData['beitrittsmonat'])) $info_beitritt .= 'Beitrittsmonat: '.$submittedData['beitrittsmonat']."\n";
				if(isset($submittedData['beitrittszustimmung'])) $info_beitritt .= 'Beitrittszustimmung: '.$submittedData['beitrittszustimmung']."\n";

				// Restliche Daten sichern
				$set = array
				(
					'tstamp'         => time(),
					'nachname'       => isset($submittedData['nachname']) ? $submittedData['nachname'] : '',
					'vorname'        => isset($submittedData['vorname']) ? $submittedData['vorname'] : '',
					'strasse'        => isset($submittedData['strasse']) ? $submittedData['strasse'] : '',
					'plz'            => isset($submittedData['plz']) ? $submittedData['plz'] : '',
					'ort'            => isset($submittedData['ort']) ? $submittedData['ort'] : '',
					//'birthday'       => isset($submittedData['geburtstag']) ? $submittedData['geburtstag'] : 0,
					'telefon1'       => isset($submittedData['telefon']) ? $submittedData['telefon'] : '',
					'email1'         => isset($submittedData['email']) ? $submittedData['email'] : '',
					'memberId'       => isset($submittedData['mitgliedsnummer']) ? $submittedData['mitgliedsnummer'] : '',
					'info_beitritt'  => $info_beitritt,
					'published'      => 1,
				);
				$objInsert = Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler %s")
				                                     ->set($set)
				                                     ->execute();
				Scope::log('[Fernschach-Verwaltung] Beitrittsformular übernommen von '.$set['nachname'].','.$set['vorname'], 'Fernschach-Verwaltung:Beitrittserklärung', ContaoContext::GENERAL);
			}
		}
	}
}
