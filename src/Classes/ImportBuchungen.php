<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class ImportBuchungen
  */
class ImportBuchungen extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Importiert eine Buchungsliste
	 */
	public function run()
	{

		if(\Input::get('key') != 'importBuchungen')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Formular wurde abgeschickt, Wortliste importieren
		if (\Input::post('FORM_SUBMIT') == 'tl_fernschach_import_buchungen')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');
			$importdatum = time(); // Importdatum setzen

			foreach ($arrUploaded as $txtFile)
			{
				$objFile = new \File($txtFile, true);

				if ($objFile->extension != 'csv')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				log_message('Importiere Datei: '.$txtFile,'fernschach-verwaltung.log');
				$resFile = $objFile->handle;
				$record_count = 0;
				$neu_count = 0;
				$update_count = 0;
				$kopf = array(); // Nimmt die Spaltennamen aus Zeile 1 auf
				$start = microtime(true);

				while(!feof($resFile))
				{
					$zeile = trim(fgets($resFile));
					if($zeile) // nur nichtleere Zeilen berücksichtigen
					{
						$spalte = explode(';', $zeile);
						if($record_count == 0)
						{
							// Kopfzeile auslesen
							$kopf = $spalte;
							log_message('Lese Kopfzeile '.$record_count.': '.$zeile,'fernschach-verwaltung.log');
							for($x = 0; $x < count($kopf); $x++)
							{
								$kopf[$x] = trim($kopf[$x]);
							}
						}
						else
						{
							log_message('Importiere Datenzeile '.$record_count.': '.$zeile,'fernschach-verwaltung.log');
							// Datensatz auslesen
							$set = array();
							$mitgliedsdaten = array();
							for($x = 0; $x < count($spalte); $x++)
							{
								$spalte[$x] = trim($spalte[$x]);
								switch($kopf[$x])
								{
									case 'betrag':
										$set['betrag'] = (double)str_replace(',', '.', $spalte[$x]); break;
									case 'typ':
										$set['typ'] = $spalte[$x]; break;
									case 'art':
										$set['art'] = $spalte[$x]; break;
									case 'kategorie':
										$set['kategorie'] = $spalte[$x]; break;
									case 'markierung':
										$set['markierung'] = $spalte[$x]; break;
									case 'reset':
										$set['resetSaldo'] = $spalte[$x]; break;
									case 'datum':
										$set['datum'] = strtotime(str_replace('.', '-', $spalte[$x])); break;
									case 'verwendungszweck':
										$set['verwendungszweck'] = $spalte[$x]; break;
									case 'turnier':
										$set['turnier'] = self::getTurnierId($spalte[$x]); break; // Turnier ggfs. erstellen
									case 'comment':
										$set['comment'] = $spalte[$x]; break;
									case 'published':
										$set['published'] = $spalte[$x]; break;
									case 'id':
										$set['id'] = (int)$spalte[$x]; break;
									// Die nächsten 3 Felder müssen noch aufgelöst werden, um eine pid zu ermitteln
									case 'memberid':
										$set['memberId'] = $spalte[$x]; break;
									case 'nachname':
										$set['nachname'] = $spalte[$x]; break;
									case 'vorname':
										$set['vorname'] = $spalte[$x]; break;
									case 'iccfid':
										$set['memberInternationalId'] = $spalte[$x]; break;
									default:
								}
							}

							// Buchungsbetrag und -typ ggfs. korrigieren
							if($set['typ'])
							{
								// Vorzeichen entfernen, da Buchungstyp gesetzt ist
								$set['betrag'] = abs($set['betrag']);
							}
							else
							{
								// Buchungstyp setzen, da nicht vorhanden
								if($set['betrag'] >= 0) $set['typ'] = 'h';
								else
								{
									$set['betrag'] = abs($set['betrag']);
									$set['typ'] = 's';
								}
							}

							// Datensatz-id prüfen
							if(!$set['id'])
							{
								// id ist nicht gesetzt, Spalte löschen
								unset($set['id']);
							}

							// memberId, nachname, vorname, memberInternationalId in eine pid auflösen
							$set['pid'] = self::getSpielerId($set['memberId'], $set['nachname'], $set['vorname'], $set['memberInternationalId']);
							// Felder löschen, da in tl_fernschach_spieler_konto unerwünscht
							unset($set['memberId']);
							unset($set['memberInternationalId']);
							unset($set['nachname']);
							unset($set['vorname']);

							// Turnierzuordnung prüfen
							//if(!isset($set['turnier']))
							//{
							//	// Feld turnier ist nicht definiert, Feld verwendungszweck verwenden wenn vorhanden
							//	if(!isset($set['verwendungszweck']))
							//	{
							//		$set['turnier'] = 0; // Felder turnier und verwendungszweck sind nicht definiert
							//	}
							//	else
							//	{
							//		$set['turnier'] = self::getTurnierId($set['verwendungszweck']); // Turnier ggfs. erstellen
							//	}
							//}

							$set['tstamp'] = time(); // Änderungsdatum setzen
							$set['importDate'] = $importdatum; // Importzeitpunkt setzen

							if($set['pid'])
							{
								// Buchung eintragen, wenn ein Spieler zugeordnet werden konnte
								log_message('Set-Array Update tl_fernschach_spieler_konto:','fernschach-verwaltung.log');
								log_message(print_r($set,true),'fernschach-verwaltung.log');
								// Neuer Datensatz
								if($set['id'])
								{
									$objInsert = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
									                                     ->set($set)
									                                     ->execute($set['id']);
									$neu_count++;
								}
								else
								{
									$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto %s")
									                                     ->set($set)
									                                     ->execute();
									$neu_count++;
								}
							}
							else
							{
								log_message('Set-Array Update failed tl_fernschach_spieler_konto - pid not found:','fernschach-verwaltung.log');
								log_message(print_r($set,true),'fernschach-verwaltung.log');
							}
						}
						$record_count++;
					}
				}

				$dauer = sprintf('%f0.4', microtime(true) - $start);
				\System::log('Buchungsimport aus Datei '.$objFile->name.' - '.($neu_count+$update_count).' Datensätze - '.$neu_count.' neu, '.$update_count.' überschrieben - Dauer: '.$dauer.'s', __METHOD__, TL_GENERAL);
			}

			// Cookie setzen und zurückkehren zur Adressenliste (key=import aus URL entfernen)
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=importBuchungen', '', \Environment::get('request')));
		}

		// Return form
		return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=importBuchungen', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

'.\Message::generate().'

<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_fernschach_import" class="tl_form tl_edit_form" method="post" enctype="multipart/form-data">

<div class="tl_formbody_edit">
	<input type="hidden" name="FORM_SUBMIT" value="tl_fernschach_import_buchungen">
	<input type="hidden" name="REQUEST_TOKEN" value="' . REQUEST_TOKEN . '">
	<input type="hidden" name="MAX_FILE_SIZE" value="' . \Config::get('maxFileSize') . '">

	<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_fernschach_buchungen_import']['headline'].'</h2>
	<p style="margin: 18px;">'.$GLOBALS['TL_LANG']['tl_fernschach_buchungen_import']['format'].'

	<div class="tl_tbox">
		<div class="widget">
			<h3>'.$GLOBALS['TL_LANG']['MSC']['source'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MSC']['source'][1]) ? '
			<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MSC']['source'][1].'</p>' : '').'
		</div>
	</div>
</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['tw_import'][0]).'">
</div>

</div>
</form>
</div>';


	}

	public function is_utf8($str)
	{
	    $strlen = strlen($str);
	    for ($i = 0; $i < $strlen; $i++) {
	        $ord = ord($str[$i]);
	        if ($ord < 0x80) continue; // 0bbbbbbb
	        elseif (($ord & 0xE0) === 0xC0 && $ord > 0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
	        elseif (($ord & 0xF0) === 0xE0) $n = 2; // 1110bbbb
	        elseif (($ord & 0xF8) === 0xF0 && $ord < 0xF5) $n = 3; // 11110bbb (exkl F5-FF)
	        else return false; // ungültiges UTF-8-Zeichen
	        for ($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
	            if (++$i === $strlen || (ord($str[$i]) & 0xC0) !== 0x80)
	                return false; // ungültiges UTF-8-Zeichen
	    }
	    return true; // kein ungültiges UTF-8-Zeichen gefunden
	}

	/**
	 * Funktion getTurnierId
	 * =====================
	 * Ermittelt zu einem Turniernamen die ID des Turniers. Existiert der Name noch nicht, wird das Turnier neu angelegt
	 * @param       string - Name des Turniers
	 * @return      id des Turniers
	 */
	function getTurnierId($string)
	{
		if(!$string) return 0; // Kein Turniername, deshalb keine Zuordnung

		$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE title = ?")
		                                     ->limit(1)
		                                     ->execute($string);

		if($objResult->numRows)
		{
			// Turnier ist vorhanden
			return $objResult->id;
		}
		else
		{
			// Turnier nicht vorhanden, neu anlegen
			$set = array
			(
				'tstamp'    => time(),
				'title'     => $string,
				'published' => '',
			);
			log_message('Set-Array Insert tl_fernschach_turniere:','fernschach-verwaltung.log');
			log_message(print_r($set,true),'fernschach-verwaltung.log');
			// Neues Turnier
			$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_turniere %s")
			                                     ->set($set)
			                                     ->execute();
			return $objInsert->insertId;
		}

	}

	/**
	 * Funktion getSpielerId
	 * =====================
	 * Ermittelt zu einem Mitgliedsnummer, einem Vor- und Nachnamen die ID des Spielers. Existiert der Spieler noch nicht, wird er neu angelegt
	 * @param memberId              integer - Mitgliedsnummer
	 * @param nachname              string - Nachname
	 * @param vorname               string - Vorname
	 * @param memberInternationalId string - ICCF-Nummer
	 * @return                      id des Spielers
	 */
	function getSpielerId($memberId, $nachname, $vorname, $memberInternationalId)
	{
		if(!$memberId && !$nachname && !$vorname && !$memberInternationalId) return 0; // Kein Spieler zuordenbar

		if($memberId)
		{
			// Nach BdF-Mitgliedsnummer suchen
			$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE memberId = ?")
			                                     ->limit(1)
			                                     ->execute($memberId);
			if($objResult->numRows)
			{
				// Spieler ist vorhanden
				return $objResult->id;
			}
			else
			{
				// Spieler nicht vorhanden, neu anlegen
				$set = array
				(
					'tstamp'                => time(),
					'memberId'              => $memberId ? $memberId : '',
					'memberInternationalId' => $memberInternationalId ? $memberInternationalId : '',
					'nachname'              => $nachname ? $nachname : '?',
					'vorname'               => $vorname ? $vorname : '?',
					'published'             => '',
				);
				log_message('Set-Array Insert tl_fernschach_spieler:','fernschach-verwaltung.log');
				log_message(print_r($set,true),'fernschach-verwaltung.log');
				// Neues Turnier
				$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler %s")
				                                     ->set($set)
				                                     ->execute();
				\System::log('Fernschach-Verwaltung: Neuer Spieler aus Buchungsimport -> '.$set['nachname'].','.$set['vorname'], __METHOD__, TL_GENERAL);
				return $objInsert->insertId;
			}
		}
		else
		{
			if($memberInternationalId)
			{
				// Nach ICCF-Mitgliedsnummer suchen
				$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE memberInternationalId = ?")
				                                     ->limit(1)
				                                     ->execute($memberInternationalId);
				if($objResult->numRows)
				{
					// Spieler ist vorhanden
					return $objResult->id;
				}
				else
				{
					// Spieler nicht vorhanden, neu anlegen
					$set = array
					(
						'tstamp'                => time(),
						'memberId'              => $memberId ? $memberId : self::setMemberId(),
						'memberInternationalId' => $memberInternationalId ? $memberInternationalId : '',
						'nachname'              => $nachname ? $nachname : '?',
						'vorname'               => $vorname ? $vorname : '?',
						'published'             => '',
					);
					log_message('Set-Array Insert tl_fernschach_spieler:','fernschach-verwaltung.log');
					log_message(print_r($set,true),'fernschach-verwaltung.log');
					// Neues Turnier
					$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler %s")
					                                     ->set($set)
					                                     ->execute();
					\System::log('Fernschach-Verwaltung: Neuer Spieler aus Buchungsimport -> '.$set['nachname'].','.$set['vorname'], __METHOD__, TL_GENERAL);
					return $objInsert->insertId;
				}
			}
			else
			{
				// Mitgliedsnummer nicht vorhanden, deshalb nach Namen suchen (Vorhandensein des Namens wurde oben schon geprüft)
				$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE nachname = ? AND vorname = ?")
				                                     ->limit(1)
				                                     ->execute($nachname, $vorname);
				if($objResult->numRows)
				{
					// mind. 1 Spieler gefunden
					return $objResult->id;
				}
				else
				{
					// Spieler nicht vorhanden, neu anlegen
					$set = array
					(
						'tstamp'    => time(),
						'memberId'  => $memberId ? $memberId : self::setMemberId(),
						'nachname'  => $nachname,
						'vorname'   => $vorname,
						'published' => '',
					);
					log_message('Set-Array Insert tl_fernschach_spieler:','fernschach-verwaltung.log');
					log_message(print_r($set,true),'fernschach-verwaltung.log');
					// Neues Turnier
					$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler %s")
					                                     ->set($set)
					                                     ->execute();
					\System::log('Fernschach-Verwaltung: Neuer Spieler aus Buchungsimport -> '.$set['nachname'].','.$set['vorname'], __METHOD__, TL_GENERAL);
					return $objInsert->insertId;
				}
			}
		}

	}

	/**
	 * Funktion setMemberId
	 * =====================
	 * Ermittelt eine neue temporäre Mitgliedsnummer mit Prefix z
	 * @return                      Nächste freie BdF-Mitgliedsnummer
	 */
	function setMemberId()
	{
		// Nächste freie BdF-Mitgliedsnummer mit z am Anfang suchen (maximal bis z9999 suchen)
		for($x = 1; $x < 10000; $x++)
		{
			$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE memberId = ?")
			                                     ->execute('z'.$x);
			if(!$objResult->numRows)
			{
				return 'z'.$x; // freie Nummer gefunden
			}
		}
		return 'z10000';
	}

}
