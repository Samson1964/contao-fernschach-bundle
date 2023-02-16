<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class Maintenance extends \Backend
{

	var $spieler = array();

	public function __construct()
	{

	}

	/**
	 * Funktion updateResetbuchungen
	 * ============================
	 * Überprüft tl_fernschach_spieler_konto auf die Gültigkeit der globalen Resetbuchung
	 */
	public function getMaintenance(\DataContainer $dc)
	{
		$update = (int)$GLOBALS['TL_CONFIG']['fernschach_maintenanceUpdate'] + 43200; // Letztes Updatedatum + 12 Stunden

		// Aktualisierung notwendig
		//if($update < time())
		if($update)
		{
			// Mitgliederkonten Frontend prüfen
			$objMember = \Database::getInstance()->prepare("SELECT * FROM tl_member")
			                                     ->execute();
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
						$mitglied = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayer->memberships);
						if($mitglied)
						{
							if($objMember->fernschach_memberId)
							{
								// Zuordnung bereits vorhanden, prüfen ob die zugeordnete ID paßt
								if($objMember->fernschach_memberId == $objPlayer->id)
								{
									// ID's stimmen überein, fertig
								}
								else
								{
									// Datensatz aktualisieren
									$set = array
									(
										'fernschach_memberId' => $objPlayer->id
									);
									\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
									                        ->set($set)
									                        ->execute($objMember->id);
									$this->createNewVersion('tl_member', $objMember->id);
									\System::log('[Fernschach-Wartung] tl_member.fernschach_memberId ('.$objMember->fernschach_memberId.') <> tl_fernschach_spieler.id ('.$objPlayer->id.'). Geändert von '.$objMember->fernschach_memberId.' auf '.$objPlayer->id.'.', __CLASS__.'::'.__FUNCTION__, TL_ERROR);
								}
							}
							else
							{
								$gruppen = self::setGroups($objMember->groups, true); // Mitgliedergruppen aktualisieren

								// Datensatz aktualisieren
								$set = array
								(
									'groups'              => $gruppen,
									'fernschach_memberId' => $objPlayer->id
								);
								\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id=?")
								                        ->set($set)
								                        ->execute($objMember->id);
								$this->createNewVersion('tl_member', $objMember->id);

								// Zuordnung noch nicht vorhanden, jetzt vornehmen
								\System::log('[Fernschach-Wartung] Neue Zuordnung FE-Mitglied ('.$objMember->username.' - '.$objMember->firstname.' '.$objMember->lastname.') zu BdF-Mitglied ('.$objPlayer->vorname.' '.$objPlayer->nachname.') vorgenommen.', __CLASS__.'::'.__FUNCTION__, TL_GENERAL);
							}
						}
						else
						{
						}
					}
					else
					{
						// Kein passender Spielerdatensatz gefunden 
					}
				}
			}
			else
			{
			}

			// Ja, Konfiguration aktualisieren
			\Contao\Config::persist('fernschach_maintenanceUpdate', time()); // Siehe https://community.contao.org/de/showthread.php?83934-In-die-localconfig-php-schreiben
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
			$key = array_search($GLOBALS['TL_CONFIG']['fernschach_memberDefault'], $gruppen, true);
			if($key !== false) unset($gruppen[$key]);
		}
		else
		{
			// Standard-Mitgliedschaft eintragen
			if($GLOBALS['TL_CONFIG']['fernschach_memberDefault']) $gruppen[] = $GLOBALS['TL_CONFIG']['fernschach_memberDefault'];
			// BdF-Mitgliedschaft entfernen
			$key = array_search($GLOBALS['TL_CONFIG']['fernschach_memberFernschach'], $gruppen, true);
			if($key !== false) unset($gruppen[$key]);
		}
		
		return serialize(array_unique($gruppen));
	}

}