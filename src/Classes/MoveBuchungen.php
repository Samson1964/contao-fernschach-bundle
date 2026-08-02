<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\Database;
use Contao\Environment;
use Contao\Input;

/**
 * Class VerschiebeBuchungen
  */
class MoveBuchungen extends Backend
{

	function __construct()
	{
	}

	/**
	 * Verschiebt Buchungen vom aktuellen Konto in die richtigen Konten
	 */
	public function run()
	{

		if(Input::get('key') != 'move')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		$id = Input::get('id'); // ID des Spielers
		$strTable = Input::get('table'); // Quelltabelle

		// Der Tabellenname wandert ungeprüft in die Abfrage und darf deshalb nur
		// eines der drei Spielerkonten sein. Ohne die Prüfung liefe ein Aufruf
		// ohne Parameter in einen SQL-Syntaxfehler.
		$erlaubt = array
		(
			'tl_fernschach_spieler_konto',
			'tl_fernschach_spieler_konto_beitrag',
			'tl_fernschach_spieler_konto_nenngeld',
		);

		if(!$id || !in_array($strTable, $erlaubt, true))
		{
			return '';
		}

		$objBuchungen = Database::getInstance()->prepare("SELECT * FROM ".$strTable." WHERE pid = ?")
		                                        ->execute($id);

		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				if($objBuchungen->kategorie == 'b' && $strTable == 'tl_fernschach_spieler_konto')
				{
					// Beitragsbuchung im Hauptkonto verschieben in das Beitragskonto
					self::Verschieben($objBuchungen, $strTable, 'tl_fernschach_spieler_konto_beitrag');
				}
				elseif($objBuchungen->kategorie == 's' && $strTable == 'tl_fernschach_spieler_konto')
				{
					// Nenngeldbuchung im Hauptkonto verschieben in das Nenngeldkonto
					self::Verschieben($objBuchungen, $strTable, 'tl_fernschach_spieler_konto_nenngeld');
				}
				elseif($objBuchungen->kategorie == 'b' && $strTable == 'tl_fernschach_spieler_konto_nenngeld')
				{
					// Beitragsbuchung im Nenngeldkonto verschieben in das Beitragskonto
					self::Verschieben($objBuchungen, $strTable, 'tl_fernschach_spieler_konto_beitrag');
				}
				elseif($objBuchungen->kategorie == 's' && $strTable == 'tl_fernschach_spieler_konto_beitrag')
				{
					// Nenngeldbuchung im Beitragskonto verschieben in das Nenngeldkonto
					self::Verschieben($objBuchungen, $strTable, 'tl_fernschach_spieler_konto_nenngeld');
				}
				elseif($objBuchungen->kategorie != 's' && $objBuchungen->kategorie != 'b' && $strTable == 'tl_fernschach_spieler_konto_beitrag')
				{
					// Diverse Buchung im Beitragskonto verschieben in das Hauptkonto
					self::Verschieben($objBuchungen, $strTable, 'tl_fernschach_spieler_konto');
				}
				elseif($objBuchungen->kategorie != 's' && $objBuchungen->kategorie != 'b' && $strTable == 'tl_fernschach_spieler_konto_nenngeld')
				{
					// Diverse Buchung im Nenngeldkonto verschieben in das Hauptkonto
					self::Verschieben($objBuchungen, $strTable, 'tl_fernschach_spieler_konto');
				}
			}
		}

		// Zurück auf die zuletzt aufgerufene Seite
		$this->redirect(str_replace('&key=move', '', Environment::get('request')));

	}

	public function Verschieben($objBuchung, $quelle, $ziel)
	{
		$set = array
		(
			'pid'              => $objBuchung->pid,
			'tstamp'           => $objBuchung->tstamp,
			'resetRecord'      => $objBuchung->resetRecord,
			'importDate'       => $objBuchung->importDate,
			'betrag'           => $objBuchung->betrag,
			'typ'              => $objBuchung->typ,
			'datum'            => $objBuchung->datum,
			'sortierung'       => $objBuchung->sortierung,
			'kategorie'        => $objBuchung->kategorie,
			'art'              => $objBuchung->art,
			'verwendungszweck' => $objBuchung->verwendungszweck,
			'markierung'       => $objBuchung->markierung,
			'saldoReset'       => $objBuchung->saldoReset,
			'turnier'          => $objBuchung->turnier,
			'comment'          => $objBuchung->comment,
			'meldungId'        => $objBuchung->meldungId,
			'published'        => $objBuchung->published
		);
		//Scope::logToFile('Verschiebe Buchung '.print_r($set, true),'fernschach-buchungen.log');
		$objInsert = Database::getInstance()->prepare("INSERT INTO ".$ziel." %s")
		                                     ->set($set)
		                                     ->execute();
		$objDelete = Database::getInstance()->prepare("DELETE FROM ".$quelle." WHERE id = ?")
		                                     ->execute($objBuchung->id);
		
		return;
	}

}
