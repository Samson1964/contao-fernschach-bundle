<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/*
 * Lädt die aktuelle Spielerliste und überschreibt die E-Mail-Adressen im definierten Newsletter-Archiv
 */

class Newsletter extends \Backend
{
	public function setNewsletter(\DataContainer $dc)
	{
		if(\Input::get('key') != 'setNewsletter')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		if(isset($GLOBALS['TL_CONFIG']['fernschach_newsletter']) && $GLOBALS['TL_CONFIG']['fernschach_newsletter'] > 0)
		{
			// Nur aktualisieren, wenn ein Newsletter-Archiv ausgewählt ist
			$arrExport = self::getRecords($dc); // Spieler auslesen

			// Aktiven Verteiler laden, um vorhandene Adrssen zu bearbeiten (aktivieren oder deaktivieren) 
			$result = \Database::getInstance()->prepare("SELECT * FROM tl_newsletter_recipients WHERE pid=?")
			                                  ->execute($GLOBALS['TL_CONFIG']['fernschach_newsletter']);
			if($result->numRows)
			{
				while($result->next())
				{
					if(isset($arrExport[$result->email]))
					{
						// Adresse soll im Verteiler aktiviert werden
						$set = array
						(
							'tstamp'        => time(),
							'fernschach_id' => $arrExport[$result->email]['id'],
							'active'        => 1
						);
						$arrExport[$result->email]['checked'] = true; // Adresse abgearbeitet
					}
					else
					{
						// Adresse im Verteiler nicht gewünscht, deshalb deaktivieren
						$set = array
						(
							'tstamp'   => time(),
							'active'   => ''
						);
					}
					\Database::getInstance()->prepare("UPDATE tl_newsletter_recipients %s WHERE id=?")
					                        ->set($set)
					                        ->execute($result->id);
				}
			}

			// Nun die restlichen Adressen in den Verteiler eintragen
			foreach($arrExport as $key => $value)
			{
				if(!$arrExport[$key]['checked'])
				{
					// Adresse noch nicht im Verteiler
					$set = array
					(
						'pid'           => $GLOBALS['TL_CONFIG']['fernschach_newsletter'],
						'tstamp'        => time(),
						'email'         => $key,
						'fernschach_id' => $arrExport[$key]['id'],
						'active'        => 1
					);
					$arrExport[$key]['checked'] = true; // Adresse abgearbeitet
					\Database::getInstance()->prepare("INSERT INTO tl_newsletter_recipients %s")
					                        ->set($set)
					                        ->execute();
				}
			}
			
		}
		
		// Cookie setzen und zurückkehren (key=setDefault aus URL entfernen)
		\System::setCookie('BE_PAGE_OFFSET', 0, 0);
		\Controller::redirect(str_replace('&key=setNewsletter', '', \Environment::get('request')));


	}

	public function getRecords(\DataContainer $dc)
	{
		// Liest die Datensätze der Fernschachverwaltung in ein Array

		// Suchbegriff in aktueller Ansicht laden
		$search = $dc->Session->get('search');
		$search = $search[$dc->table]; // Das Array enthält field und value
		//if($search['field']) $sql = " WHERE ".$search['field']." LIKE '%%".$search['value']."%%'"; // findet auch Umlaute, Suche nach "ba" findet auch "bä"
		if($search['field'] && $search['value']) $sql = " WHERE LOWER(CAST(".$search['field']." AS CHAR)) REGEXP LOWER('".$search['value']."')"; // Contao-Standard, ohne Umlaute, Suche nach "ba" findet nicht "bä"
		else $sql = '';

		// Filter in aktueller Ansicht laden. Beispiel mit Spezialfilter (tli_filter):
		//
		// [filter] => Array
		//       (
		//           [tl_lizenzverwaltungFilter] => Array
		//               (
		//                   [tli_filter] => V2
		//               )
		//
		//           [tl_lizenzverwaltung] => Array
		//               (
		//                   [limit] => 0,30
		//                   [geschlecht] => w
		//               )
		//
		//       )
		$filter = $dc->Session->get('filter');
		$filter = $filter[$dc->table]; // Das Array enthält limit (Wert meistens = 0,30) und alle Feldnamen mit den Werten
		log_message(print_r($filter, true), 'fernschachverwaltung.log');
		foreach($filter as $key => $value)
		{
			if($key != 'limit')
			{
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " ".$key." = '".$value."'";
			}
		}

		// Spezialfilter berücksichtigen
		$filter = $dc->Session->get('filter');
		$filter = $filter[$dc->table.'Filter'] = array('tfs_filter'); // Wert aus Spezialfilter
		switch($filter)
		{
			case '2': // Geburtsdatum fehlt
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " birthday = 0 OR birthday = ''";
				break;

			case '3': // ICCF-Nummer fehlt
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " memberInternationalId = ''";
				break;

			case '4': // E-Mail fehlt
				($sql) ? $sql .= ' AND' : $sql = ' WHERE';
				$sql .= " email1 = '' AND email2 = ''";
				break;

			default:
		}

		($sql) ? $sql .= " ORDER BY nachname,vorname ASC" : $sql = " ORDER BY nachname,vorname ASC";

		$sql = "SELECT * FROM tl_fernschach_spieler".$sql;

		log_message('E-Mail-Export mit: '.$sql, 'fernschachverwaltung.log');
		// Datensätze laden
		$records = \Database::getInstance()->prepare($sql)
		                                   ->execute();

		// Datensätze umwandeln
		$arrExport = array();
		if($records->numRows)
		{
			while($records->next())
			{
				$exportieren = true;
				switch($filter)
				{
					case '1': // Alle Mitglieder
						// Mitgliedschaften prüfen (memberships)
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($records->memberships);
						break;

					case '8': // Alle Nichtmitglieder
						// Nichtmitgliedschaften prüfen (memberships)
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($records->memberships);
						// Wahr/Falsch umdrehen
						if($exportieren) $exportieren = false;
						else $exportieren = true;
						break;

					case '101': // Mitgliedsende 31.12. nächstes Jahr
					case '100': // Mitgliedsende 31.12. dieses Jahr
					case '99': // Mitgliedsende 31.12. letztes Jahr
					case '98': // Mitgliedsende 31.12. minus 2 Jahre
					case '97': // Mitgliedsende 31.12. minus 3 Jahre
					case '96': // Mitgliedsende 31.12. minus 4 Jahre
					case '95': // Mitgliedsende 31.12. minus 5 Jahre
					case '94': // Mitgliedsende 31.12. minus 6 Jahre
					case '93': // Mitgliedsende 31.12. minus 7 Jahre
					case '92': // Mitgliedsende 31.12. minus 8 Jahre
					case '91': // Mitgliedsende 31.12. minus 9 Jahre
						// Mitgliedsende prüfen (memberships)
						$datum = (date('Y') + $filter - 100).'1231';
						$exportieren = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchMembership($records->memberships, $datum);
						break;

					default:
				}
				if($exportieren)
				{
					if($records->email1) $arrExport[$records->email1] = array('id' => $records->id, 'checked' => false);
					if($records->email2) $arrExport[$records->email2] = array('id' => $records->id, 'checked' => false);
				}
			}
		}
		return $arrExport;
	}

}
