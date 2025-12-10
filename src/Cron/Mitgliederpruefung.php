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
class Mitgliederpruefung
{
	private ContaoFramework $framework;

	/**
	 * Make the constuctor public
	 */
	public function __construct(ContaoFramework $framework)
	{
		//\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] zzz Mitglieder wurden überprüft');
	}

	/**
	 * @CronJob("minutely")
	 */
	public function onMinutely(): void
	{
		//\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] yyy Mitglieder wurden überprüft');
		// Alle Mitgliederkonten suchen, 
		// 1) deren letzte Aktualisierung vor der letzten Wartung (fernschach_memberbridgeTime) erfolgte
		// 2) die nach der letzten Wartung aktualisiert (tstamp) wurden 
		$aktuelleZeit = time();
		$updatezeit = $aktuelleZeit - $GLOBALS['TL_CONFIG']['fernschach_intervall_memberbridgeCheck']; // Aktuelle Zeit minus eingestelltem Intervall
		//$objMember = \Database::getInstance()->prepare("SELECT * FROM tl_member WHERE fernschach_memberbridgeTime <= ? AND tstamp <= ? AND locked = ? AND disable = ? AND username != ?")
		//                                     ->execute($updatezeit, $updatezeit,'', '', '');
		$objMember = \Database::getInstance()->prepare("SELECT * FROM tl_member WHERE locked = ? AND disable = ? AND username != ?")
		                                     ->execute('', '', '');
		$meldung = $objMember->numRows.' Frontend-Mitglieder müssen geprüft werden.<br>';

		if($objMember->numRows)
		{
			// Alle Frontend-Mitglieder der Reihe nach prüfen
			while($objMember->next())
			{
				// E-Mail-Adresse in Fernschach-Verwaltung suchen
				$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE (email1 = ? OR email2 = ?) AND published = ?")
				                                     ->execute($objMember->email, $objMember->email, 1);

				if($objPlayer->numRows)
				{
					// Gefundene Spieler prüfen
					while($objPlayer->next())
					{
						// Ist der Spieler Mitglied im BdF?
						$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer->memberships, NULL, $objPlayer->published);

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
										\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
										                        ->set($set)
										                        ->execute($objMember->id);
										$version = new \Versions('tl_member', $objMember->id);
										$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
										$version->create();

										// Zuordnung entfernen
										$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu Gruppe BdF-Mitglied hinzugefügt.<br>';
										//\System::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu Gruppe BdF-Mitglied hinzugefügt.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
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
									\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
									                        ->set($set)
									                        ->execute($objMember->id);
									$version = new \Versions('tl_member', $objMember->id);
									$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
									$version->create();
									//\System::log('[Fernschach-Wartung] tl_member.fernschach_memberId ('.$objMember->fernschach_memberId.') <> tl_fernschach_spieler.id ('.$objPlayer->id.'). Geändert von '.$objMember->fernschach_memberId.' auf '.$objPlayer->id.'.', __CLASS__.'::'.__FUNCTION__, TL_ERROR);
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
								\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								$version = new \Versions('tl_member', $objMember->id);
								$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
								$version->create();

								// Zuordnung noch nicht vorhanden, jetzt vornehmen
								$meldung .= 'Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.' ['.$objPlayer->id.']) vorgenommen.<br>';
								//\System::log('[Fernschach-Wartung] Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.' ['.$objPlayer->id.']) vorgenommen.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
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
								\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								$version = new \Versions('tl_member', $objMember->id);
								$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
								$version->create();

								// Zuordnung entfernen
								$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt, da ausgetreten.<br>';
								//\System::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt, da ausgetreten.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
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
						\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
						                        ->set($set)
						                        ->execute($objMember->id);
						$version = new \Versions('tl_member', $objMember->id);
						$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
						$version->create();

						// Zuordnung entfernen
						$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt.';
						//\System::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
					}
				}
			}
		}
		
		$file = TL_ROOT.'/vendor/schachbulle/contao-fernschach-bundle/src/Resources/mitgliederpruefung.txt';
		file_put_contents($file, $meldung);

		// Log-Eintrag vornehmen
		\System::getContainer()->get('monolog.logger.contao.cron')->info('[Fernschach-Wartung] Mitglieder wurden überprüft');
		
	}

	/**
	 * Funktion setGroups
	 *
	 * param $value      Serialisiertes Array mit den Mitgliedergruppen aus tl_member.groups
	 * param $status     TRUE = Mitgliedschaft eintragen, FALSE = Mitgliedschaft austragen
	 * return array      Aktualisiertes serialisiertes Array
	 */
	public function setGroups($value, $status)
	{
		$gruppen = (array)unserialize($value); // Mitgliedergruppen in Array umwandeln

		if($status)
		{
			// BdF-Mitgliedschaft eintragen
			if($GLOBALS['TL_CONFIG']['fernschach_memberFernschach']) $gruppen[] = $GLOBALS['TL_CONFIG']['fernschach_memberFernschach'];
			// Standard-Mitgliedschaft entfernen
			$key = array_search($GLOBALS['TL_CONFIG']['fernschach_memberDefault'], $gruppen);
			if(isset($key)) unset($gruppen[$key]);
		}
		else
		{
			// Standard-Mitgliedschaft eintragen
			if($GLOBALS['TL_CONFIG']['fernschach_memberDefault']) $gruppen[] = $GLOBALS['TL_CONFIG']['fernschach_memberDefault'];
			// BdF-Mitgliedschaft entfernen
			$key = array_search($GLOBALS['TL_CONFIG']['fernschach_memberFernschach'], $gruppen);
			if(isset($key)) unset($gruppen[$key]);
		}

		return serialize(array_unique($gruppen));
	}

}
