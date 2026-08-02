<?php

namespace Schachbulle\ContaoFernschachBundle\Cron;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\StringUtil;
use Contao\System;
use Contao\Versions;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

/**
 * Gleicht die Frontend-Mitglieder mit den BdF-Spielerdatensätzen ab.
 *
 * Geprüft wird stündlich, ob jedes Frontend-Mitglied dem richtigen Spieler
 * zugeordnet ist und ob die Gruppenzugehörigkeit "BdF-Mitglied" zur
 * tatsächlichen Mitgliedschaft passt. Abweichungen werden korrigiert und
 * protokolliert.
 */
class Mitgliederpruefung
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


		//// Log-Eintrag vornehmen
		//System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] checkGroup');
		//$objMember = Database::getInstance()->prepare("SELECT * FROM tl_member WHERE id = ?")
		//                                     ->execute(1);
        //
		//System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] objMember->groups: '.print_r($objMember->groups, true));
		//$gruppen = self::setGroups($objMember->groups, true);
		//System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] gruppen: '.print_r($gruppen, true));

		// ================================================================
		// Prüfung tl_member auf Mitgliedschaft im BdF
		// ================================================================
		//$result = self::check_tl_member();
		//return;
		
		//System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] yyy Mitglieder wurden überprüft');
		// Alle Mitgliederkonten suchen, 
		// 1) deren letzte Aktualisierung vor der letzten Wartung (fernschach_memberbridgeTime) erfolgte
		// 2) die nach der letzten Wartung aktualisiert (tstamp) wurden 
		$aktuelleZeit = time();
		$updatezeit = $aktuelleZeit - Config::get('fernschach_intervall_memberbridgeCheck'); // Aktuelle Zeit minus eingestelltem Intervall
		// Die Spalte tl_member.locked (Sperre nach zu vielen Fehlanmeldungen) gibt
		// es nur bis Contao 4.13. Unter Contao 5 entfällt die Bedingung, sonst
		// bräche die Abfrage mit "Unknown column" ab.
		$locked = Database::getInstance()->fieldExists('locked', 'tl_member') ? ' AND locked = ?' : '';
		$parameter = $locked ? array('', '', '') : array('', '');

		$objMember = Database::getInstance()->prepare("SELECT * FROM tl_member WHERE disable = ? AND username != ?".$locked)
		                                     ->execute(...$parameter);
		$meldung = $objMember->numRows.' Frontend-Mitglieder müssen geprüft werden.<br>';

		if($objMember->numRows)
		{
			// Alle Frontend-Mitglieder der Reihe nach prüfen
			while($objMember->next())
			{
				// E-Mail-Adresse in Fernschach-Verwaltung suchen
				$objPlayer = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE (email1 = ? OR email2 = ?) AND published = ?")
				                                     ->execute($objMember->email, $objMember->email, 1);

				if($objPlayer->numRows)
				{
					// Gefundene Spieler prüfen
					while($objPlayer->next())
					{
						// Ist der Spieler Mitglied im BdF?
						$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer, NULL, $objPlayer->published);

						if($mitglied)
						{
							// Spieler ist Mitglied und veröffentlicht
							if($objMember->fernschach_memberId)
							{
								// Zuordnung bereits vorhanden, prüfen ob die zugeordnete ID paßt
								if($objMember->fernschach_memberId == $objPlayer->id)
								{
									// ID's stimmen überein, jetzt Mitgliedergruppen prüfen
									$gruppen = self::setGroups($objMember->groups, true); // Mitgliedergruppen aktualisieren, BdF-Mitglied eintragen
									if($gruppen != $objMember->groups)
									{
										// Aktualisierung tl_member.groups notwendig
										$set = array
										(
											'tstamp'                      => $aktuelleZeit,
											'fernschach_memberbridgeTime' => $aktuelleZeit,
											'groups'                      => $gruppen
										);
										Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
										                        ->set($set)
										                        ->execute($objMember->id);
										//$version = new Versions('tl_member', $objMember->id);
										//$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
										//$version->create();

										// Zuordnung entfernen
										$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu Gruppe BdF-Mitglied hinzugefügt.<br>';
										//Scope::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu Gruppe BdF-Mitglied hinzugefügt.', __CLASS__.'::'.__FUNCTION__, ContaoContext::GENERAL);
									}
								}
								else
								{
									$gruppen = self::setGroups($objMember->groups, false); // Mitgliedergruppen aktualisieren, BdF-Mitglied austragen

									// Datensatz aktualisieren
									$set = array
									(
										'tstamp'                      => $aktuelleZeit,
										'fernschach_memberbridgeTime' => $aktuelleZeit,
										'groups'                      => $gruppen,
										'fernschach_memberId'         => $objPlayer->id
									);
									Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
									                        ->set($set)
									                        ->execute($objMember->id);
									// Auskommentiert wegen ErrorException: Warning: Attempt to read property "server" on null in /kunden/107305_14053/webseiten/schachbund/dsbweb-entwicklung.2023/vendor/contao/core-bundle/src/Resources/contao/classes/Versions.php
									//$version = new Versions('tl_member', $objMember->id);
									//$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
									//$version->create();
									//Scope::log('[Fernschach-Wartung] tl_member.fernschach_memberId ('.$objMember->fernschach_memberId.') <> tl_fernschach_spieler.id ('.$objPlayer->id.'). Geändert von '.$objMember->fernschach_memberId.' auf '.$objPlayer->id.'.', __CLASS__.'::'.__FUNCTION__, ContaoContext::ERROR);
								}
							}
							else
							{
								// Es gibt noch keine Zuordnung eines Mitglieds
								$gruppen = self::setGroups($objMember->groups, true); // Mitgliedergruppen aktualisieren, BdF-Mitglied eintragen

								// Datensatz aktualisieren
								$set = array
								(
									'tstamp'                      => $aktuelleZeit,
									'fernschach_memberbridgeTime' => $aktuelleZeit,
									'groups'                      => $gruppen,
									'fernschach_memberId'         => $objPlayer->id
								);
								Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								//$version = new Versions('tl_member', $objMember->id);
								//$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
								//$version->create();

								// Zuordnung noch nicht vorhanden, jetzt vornehmen
								$meldung .= 'Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.' ['.$objPlayer->id.']) vorgenommen.<br>';
								//Scope::log('[Fernschach-Wartung] Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.' ['.$objPlayer->id.']) vorgenommen.', __CLASS__.'::'.__FUNCTION__, ContaoContext::GENERAL);
							}
						}
						else
						{
							// Spieler ist kein Mitglied mehr, Zuordnung ggfs. entfernen
							$gruppen = self::setGroups($objMember->groups, false); // Mitgliedergruppen aktualisieren, BdF-Mitglied austragen
							if($objMember->fernschach_memberId > 0 || $gruppen != $objMember->groups)
							{
								// Aktualisierung tl_member notwendig
								$set = array
								(
									'tstamp'                      => $aktuelleZeit,
									'fernschach_memberbridgeTime' => $aktuelleZeit,
									'groups'                      => $gruppen,
									'fernschach_memberId'         => 0
								);
								Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								//$version = new Versions('tl_member', $objMember->id);
								//$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
								//$version->create();

								// Zuordnung entfernen
								$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt, da ausgetreten.<br>';
								//Scope::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt, da ausgetreten.', __CLASS__.'::'.__FUNCTION__, ContaoContext::GENERAL);
							}
						}
					}
				}
				else
				{
					// Kein passender Spielerdatensatz gefunden, deshalb Zuordnung prüfen
					$gruppen = self::setGroups($objMember->groups, false); // Mitgliedergruppen aktualisieren, BdF-Mitglied austragen
					if($objMember->fernschach_memberId > 0 || $gruppen != $objMember->groups)
					{
						// Aktualisierung tl_member notwendig
						$set = array
						(
							'tstamp'                      => $aktuelleZeit,
							'fernschach_memberbridgeTime' => $aktuelleZeit,
							'groups'                      => $gruppen,
							'fernschach_memberId'         => 0
						);
						Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
						                        ->set($set)
						                        ->execute($objMember->id);
						//$version = new Versions('tl_member', $objMember->id);
						//$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
						//$version->create();

						// Zuordnung entfernen
						$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt.';
						//Scope::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt.', __CLASS__.'::'.__FUNCTION__, ContaoContext::GENERAL);
					}
				}
			}
		}
		
		$meldung .= 'Prüfung beendet. <span style="color:#575757;"><i>(Letzte Prüfung: '.date('d.m.Y H:i').')</i></span>';
		$file = System::getContainer()->getParameter('kernel.project_dir').'/system/tmp/contao-fernschach-bundle_mitgliederpruefung.txt';
		file_put_contents($file, $meldung);

		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitglieder wurden überprüft');
		
	}

	public function checkGroup()
	{
		// Log-Eintrag vornehmen
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] checkGroup');
		$objMember = Database::getInstance()->prepare("SELECT * FROM tl_member WHERE id = ?")
		                                     ->execute(1);

		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] objMember->groups: '.print_r($objMember->groups, true));
		$gruppen = self::setGroups($objMember->groups, true);
		System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] gruppen: '.print_r($gruppen, true));

	}

	///**
	// * Funktion setGroups
	// *
	// * param $value      Serialisiertes Array mit den Mitgliedergruppen aus tl_member.groups
	// * param $status     TRUE = Mitgliedschaft eintragen, FALSE = Mitgliedschaft austragen
	// * return array      Aktualisiertes serialisiertes Array
	// */
	//public function setGroups($value, $status)
	//{
	//	$gruppen = StringUtil::deserialize($value); // Mitgliedergruppen in Array umwandeln
    //
	//	if($status)
	//	{
	//		// BdF-Mitgliedschaft eintragen
	//		if(Config::get('fernschach_memberFernschach')) $gruppen[] = Config::get('fernschach_memberFernschach');
	//		// Standard-Mitgliedschaft entfernen
	//		$key = array_search(Config::get('fernschach_memberDefault'), $gruppen);
	//		if(isset($key)) unset($gruppen[$key]);
	//	}
	//	else
	//	{
	//		// Standard-Mitgliedschaft eintragen
	//		if(Config::get('fernschach_memberDefault')) $gruppen[] = Config::get('fernschach_memberDefault');
	//		// BdF-Mitgliedschaft entfernen
	//		$key = array_search(Config::get('fernschach_memberFernschach'), $gruppen);
	//		if(isset($key)) unset($gruppen[$key]);
	//	}
    //
	//	return serialize(array_unique($gruppen));
	//}

	/**
	 * Funktion setGroups
	 *
	 * param $value      Serialisierter String mit den Mitgliedergruppen aus tl_member.groups
	 * param $status     TRUE = Mitgliedschaft eintragen, FALSE = Mitgliedschaft austragen
	 * return string     Aktualisierter serialisierter String
	 */
	public function setGroups($value, $status)
	{
		$gruppen = StringUtil::deserialize($value, true); // Deserialisieren, um das Gruppen-Array wiederherzustellen
		if(!is_array($gruppen)) $gruppen = array();

		if($status)
		{
			// BdF-Mitgliedschaft eintragen
			if(Config::get('fernschach_memberFernschach')) $gruppen[] = (string)Config::get('fernschach_memberFernschach');
			// Standard-Mitgliedschaft entfernen
			$key = array_search(Config::get('fernschach_memberDefault'), $gruppen);
			if(isset($key)) unset($gruppen[$key]);
		}
		else
		{
			// Standard-Mitgliedschaft eintragen
			if(Config::get('fernschach_memberDefault')) $gruppen[] = (string)Config::get('fernschach_memberDefault');
			// BdF-Mitgliedschaft entfernen
			$key = array_search(Config::get('fernschach_memberFernschach'), $gruppen);
			if(isset($key)) unset($gruppen[$key]);
		}

		return serialize(array_unique($gruppen));
	}

}
