<?php
namespace Schachbulle\ContaoFernschachBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Form;

#[AsHook('processFormData')]
class ProcessFormDataListener
{
	public function __invoke(array $submittedData, array $formData, array|null $files, array $labels, Form $form): void
	{
		if(isset($GLOBALS['TL_CONFIG']['fernschach_beitrittsformular']) > 0)
		{
			// Ein Beitrittsformular wurde konfiguriert/zugewiesen
			if($GLOBALS['TL_CONFIG']['fernschach_beitrittsformular'] == $formData['id'])
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
				$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler %s")
				                                     ->set($set)
				                                     ->execute();
				\System::log('[Fernschach-Verwaltung] Beitrittsformular übernommen von '.$set['nachname'].','.$set['vorname'], 'Fernschach-Verwaltung:Beitrittserklärung', 'LOG_INFO');
			}
		}
		//$log = 'submittedData:';
		//$log .= print_r($submittedData, true);
		//$log .= 'formData:';
		//$log .= print_r($formData, true);
		//log_message($log, 'test.log');

		// Do something …
	}
}
