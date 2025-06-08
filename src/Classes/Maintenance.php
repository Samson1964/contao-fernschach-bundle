<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class Maintenance extends \Backend
{

	var $spieler = array();

	public function __construct()
	{

	}

	/**
	 * Funktion getMaintenance
	 * ============================
	 * Wartungsfunktionen ausführen
	 */
	public function getMaintenance(\DataContainer $dc)
	{
		$update = (int)$GLOBALS['TL_CONFIG']['fernschach_maintenanceUpdate'] + $GLOBALS['TL_CONFIG']['fernschach_maintenanceUpdate_time']; // Letztes Updatedatum + eingestellter Rhythmus
		$meldung = '<br>';

		// Aktualisierung notwendig, da die Wartezeit abgelaufen ist
		if($update < time())
		{
			$updatezeit = time() - $GLOBALS['TL_CONFIG']['fernschach_maintenanceUpdate_time'];
			// Alle Mitgliederkonten suchen, deren letzte Aktualisierung länger als die Wartezeit zurückliegt
			$objMember = \Database::getInstance()->prepare("SELECT * FROM tl_member WHERE tstamp <= ?")
			                                     ->execute($updatezeit);

			if($objMember->numRows)
			{
				while($objMember->next())
				{
					// E-Mail-Adresse in Fernschach-Verwaltung suchen
					$objPlayer = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE email1 = ? OR email2 = ?")
					                                     ->execute($objMember->email, $objMember->email);

					if($objPlayer->numRows)
					{
						// Datensatz gefunden. Ist der Spieler Mitglied im BdF?
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
											'groups'              => $gruppen
										);
										\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
										                        ->set($set)
										                        ->execute($objMember->id);
										$version = new \Versions('tl_member', $objMember->id);
										$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
										$version->create();

										// Zuordnung entfernen
										$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu Gruppe BdF-Mitglied hinzugefügt.<br>';
										\System::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu Gruppe BdF-Mitglied hinzugefügt.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
									}
								}
								else
								{
									$gruppen = self::setGroups($objMember->groups, false); // Mitgliedergruppen aktualisieren, BdF-Mitglied austragen

									// Datensatz aktualisieren
									$set = array
									(
										'groups'              => $gruppen,
										'fernschach_memberId' => $objPlayer->id
									);
									\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
									                        ->set($set)
									                        ->execute($objMember->id);
									$version = new \Versions('tl_member', $objMember->id);
									$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
									$version->create();
									\System::log('[Fernschach-Wartung] tl_member.fernschach_memberId ('.$objMember->fernschach_memberId.') <> tl_fernschach_spieler.id ('.$objPlayer->id.'). Geändert von '.$objMember->fernschach_memberId.' auf '.$objPlayer->id.'.', __CLASS__.'::'.__FUNCTION__, TL_ERROR);
								}
							}
							else
							{
								$gruppen = self::setGroups($objMember->groups, true); // Mitgliedergruppen aktualisieren, BdF-Mitglied eintragen

								// Datensatz aktualisieren
								$set = array
								(
									'groups'              => $gruppen,
									'fernschach_memberId' => $objPlayer->id
								);
								\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								$version = new \Versions('tl_member', $objMember->id);
								$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
								$version->create();

								// Zuordnung noch nicht vorhanden, jetzt vornehmen
								$meldung .= 'Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.') vorgenommen.<br>';
								\System::log('[Fernschach-Wartung] Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.') vorgenommen.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
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
									'groups'              => $gruppen,
									'fernschach_memberId' => 0
								);
								\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								$version = new \Versions('tl_member', $objMember->id);
								$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
								$version->create();

								// Zuordnung entfernen
								$meldung .= 'Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt, da ausgetreten.<br>';
								\System::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt, da ausgetreten.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
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
								'groups'              => $gruppen,
								'fernschach_memberId' => 0
							);
							\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
							                        ->set($set)
							                        ->execute($objMember->id);
							$version = new \Versions('tl_member', $objMember->id);
							$version->setUsername($GLOBALS['TL_LANG']['fernschachverwaltung']['botname']);
							$version->create();

							// Zuordnung entfernen
							\System::log('[Fernschach-Wartung] Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied entfernt.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
						}
					}
				}
			}
			else
			{
			}

			// Ja, Konfiguration aktualisieren
			\Contao\Config::persist('fernschach_maintenanceUpdate', time()); // Siehe https://community.contao.org/de/showthread.php?83934-In-die-localconfig-php-schreiben
			// Meldung ausgeben
			$backendlink = $this->replaceInsertTags('{{env::url}}').'/contao?do=log';
			\Message::addConfirmation('Wartung wurde ausgeführt (Details im <a href="'.$backendlink.'">System-Log</a>)'.$meldung);

			//$zeitmessung->Stop();
		}

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
