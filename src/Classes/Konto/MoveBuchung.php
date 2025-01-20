<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Konto;

/**
 * Class VerschiebeBuchungen
  */
class MoveBuchung extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Verschiebt Buchungen vom aktuellen Konto in die richtigen Konten
	 */
	public function run()
	{

		if(\Input::get('key') != 'moveBuchung')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}
		if(\Input::get('source') == false)
		{
			// Beenden, wenn keine Quelle angegeben
			return '';
		}
		if(\Input::get('target') == false)
		{
			// Beenden, wenn kein Ziel angegeben
			return '';
		}

		$id = \Input::get('id'); // ID der Buchung
		switch(\Input::get('source'))
		{
			case 'h': $source = ''; break;
			case 'b': $source = '_beitrag'; break;
			case 'n': $source = '_nenngeld'; break;
		}
		switch(\Input::get('target'))
		{
			case 'h': $target = ''; break;
			case 'b': $target = '_beitrag'; break;
			case 'n': $target = '_nenngeld'; break;
		}

		$objBuchung = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto".$source." WHERE id = ?")
		                                      ->execute($id);

		if($objBuchung->numRows)
		{
			self::Verschieben($objBuchung, 'tl_fernschach_spieler_konto'.$source, 'tl_fernschach_spieler_konto'.$target);
		}

		// Request bearbeiten
		$request = \Environment::get('request');
		$request = str_replace('&id='.\Input::get('id'), '&id='.$objBuchung->pid, $request); // Buchungs-ID durch Spieler-ID ersetzen
		$request = str_replace('&key=moveBuchung&source='.\Input::get('source').'&target='.\Input::get('target'), '', $request); // Funktionsaufruf entfernen
		// Zurück auf die zuletzt aufgerufene Seite
		$this->redirect($request);

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
			'kategorie'        => $ziel == 'tl_fernschach_spieler_konto_beitrag' ? 'b' : ($ziel == 'tl_fernschach_spieler_konto_nenngeld' ? 's' : $objBuchung->kategorie),
			'art'              => $objBuchung->art,
			'verwendungszweck' => $objBuchung->verwendungszweck,
			'markierung'       => $objBuchung->markierung,
			'saldoReset'       => $objBuchung->saldoReset,
			'turnier'          => $objBuchung->turnier,
			'comment'          => $objBuchung->comment,
			'meldungId'        => $objBuchung->meldungId,
			'published'        => $objBuchung->published
		);
		//log_message('Verschiebe Buchung '.print_r($set, true),'fernschach-buchungen.log');
		$objInsert = \Database::getInstance()->prepare("INSERT INTO ".$ziel." %s")
		                                     ->set($set)
		                                     ->execute();
		$objDelete = \Database::getInstance()->prepare("DELETE FROM ".$quelle." WHERE id = ?")
		                                     ->execute($objBuchung->id);
		
		return;
	}

}
