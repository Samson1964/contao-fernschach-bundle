<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;

/**
 * Räumt die Nenngeldbuchungen einer gelöschten Anmeldung oder Bewerbung ab.
 *
 * Eine Anmeldung hinterlässt eine Sollbuchung auf dem Nenngeldkonto des
 * Spielers. Wird die Anmeldung gelöscht, muss die Forderung mit verschwinden —
 * sonst schuldet der Spieler Geld für ein Turnier, zu dem er nicht gemeldet ist.
 *
 * Zwei Fälle sind zu unterscheiden:
 *
 * 1. Die Buchung trägt in `meldungId` die Nummer der Anmeldung. Dann ist die
 *    Zuordnung eindeutig und die Buchung wird ohne Rückfrage mitgelöscht.
 * 2. Die Buchung trägt keine `meldungId`, passt aber nach Spieler und Turnier.
 *    So entstehen die Buchungen der Mannschaftsmeldungen und alle Datensätze
 *    aus der Zeit vor dieser Verknüpfung. Hier wird **nicht** gelöscht: Meldet
 *    ein Mannschaftsleiter zwei Mannschaften zum selben Turnier, gehören beide
 *    Buchungen zu verschiedenen Meldungen, und ein Automatismus würde die
 *    falsche erwischen. Stattdessen erscheint im Backend ein Hinweis, der die
 *    gefundenen Buchungen benennt und zum Löschen anbietet.
 */
class Nenngeldbuchungen extends Backend
{
	/**
	 * Schlüssel, unter dem die gefundenen Waisen in der Backend-Sitzung liegen.
	 */
	private const SITZUNGSSCHLUESSEL = 'fernschach_nenngeld_waisen';

	/**
	 * Wert des Parameters `key`, mit dem der Hinweis das Löschen auslöst.
	 */
	private const AKTION = 'nenngeldwaisen';

	/**
	 * Löscht die eindeutig zugeordneten Buchungen und merkt sich die übrigen.
	 *
	 * Gedacht als `ondelete_callback` von tl_fernschach_turniere_meldungen und
	 * tl_fernschach_turniere_bewerbungen. Contao ruft den Rückruf auf, bevor der
	 * Datensatz verschwindet, sodass hier noch alle Angaben vorliegen.
	 *
	 * Jede gelöschte Buchung wird ins Systemprotokoll geschrieben. Anders als
	 * beim Datensatz selbst gibt es für sie keine Wiederherstellung über den
	 * Papierkorb; das Protokoll ist also die einzige Spur.
	 *
	 * @param DataContainer $dc Der Datensatz, der gerade gelöscht wird
	 *
	 * @return void Seiteneffekt ist das Löschen in
	 *              tl_fernschach_spieler_konto_nenngeld sowie ein Vermerk in der
	 *              Backend-Sitzung für die nicht eindeutig zuzuordnenden Buchungen
	 */
	public static function beimLoeschen(DataContainer $dc): void
	{
		if (!$dc->activeRecord)
		{
			return;
		}

		$intMeldung = (int) $dc->activeRecord->id;
		$intTurnier = (int) $dc->activeRecord->pid;
		$intSpieler = (int) self::ermittleSpieler($dc->activeRecord);

		// Fall 1: eindeutig über die Meldungsnummer verknüpft
		$objBuchungen = Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler_konto_nenngeld WHERE meldungId = ?')
		                                        ->execute($intMeldung);

		while ($objBuchungen->next())
		{
			Scope::log(
				'[Fernschach-Verwaltung] Nenngeldbuchung '.$objBuchungen->id.' ('.self::beschreibe($objBuchungen).') mit der Meldung '.$intMeldung.' gelöscht.',
				__METHOD__,
				ContaoContext::GENERAL
			);
		}

		Database::getInstance()->prepare('DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE meldungId = ?')
		                        ->execute($intMeldung);

		// Fall 2: passt nach Spieler und Turnier, trägt aber keine Meldungsnummer
		if (!$intSpieler || !$intTurnier)
		{
			return;
		}

		$objWaisen = Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler_konto_nenngeld WHERE pid = ? AND turnier = ? AND (meldungId = ? OR meldungId IS NULL) AND typ = ?')
		                                     ->execute($intSpieler, $intTurnier, 0, 's');

		if (!$objWaisen->numRows)
		{
			return;
		}

		$arrWaisen = self::hole();

		while ($objWaisen->next())
		{
			$arrWaisen[(int) $objWaisen->id] = self::beschreibe($objWaisen);
		}

		Scope::setBackendSessionValue(self::SITZUNGSSCHLUESSEL, $arrWaisen);
	}

	/**
	 * Zeigt den Hinweis auf offene Buchungen und führt das Löschen aus.
	 *
	 * Gedacht als `onload_callback`. Der Rückruf erledigt beides, weil Contao
	 * die Liste nach dem Löschen neu aufbaut: Beim ersten Aufbau erscheint der
	 * Hinweis, beim Aufruf des Links im Hinweis wird gelöscht.
	 *
	 * @return void Gibt Meldungen über Contaos Message-Klasse aus
	 */
	public static function hinweisUndAktion(): void
	{
		if (Input::get('key') === self::AKTION)
		{
			self::loescheGemerkte();

			return;
		}

		$arrWaisen = self::hole();

		if (!$arrWaisen)
		{
			return;
		}

		$strListe = '<ul style="margin:.5em 0 .5em 1.5em">';

		foreach ($arrWaisen as $strBeschreibung)
		{
			$strListe .= '<li>'.StringUtil::specialchars($strBeschreibung).'</li>';
		}

		$strListe .= '</ul>';

		$strFrage = 1 === \count($arrWaisen)
			? 'Diese Nenngeldbuchung wirklich löschen?'
			: 'Diese '.\count($arrWaisen).' Nenngeldbuchungen wirklich löschen?';

		$strLink = self::addToUrl('key='.self::AKTION);

		Message::addInfo(
			'Zur gelöschten Meldung gab es Nenngeldbuchungen ohne Verknüpfung, die nach Spieler und Turnier passen. Sie wurden <b>nicht</b> mitgelöscht, weil sie auch zu einer anderen Meldung gehören könnten:'
			.$strListe
			.'<a href="'.StringUtil::specialchars($strLink).'" class="tl_submit" style="display:inline-block" onclick="return confirm(\''.$strFrage.'\')">Buchungen jetzt löschen</a> '
			.'<a href="'.StringUtil::specialchars(self::addToUrl('key='.self::AKTION.'&amp;verwerfen=1')).'" style="margin-left:1em">Hinweis verwerfen</a>'
		);
	}

	/**
	 * Löscht die in der Sitzung gemerkten Buchungen — oder verwirft den Hinweis.
	 *
	 * @return void Leert in beiden Fällen den Sitzungseintrag und kehrt in die
	 *              Liste zurück
	 */
	private static function loescheGemerkte(): void
	{
		$arrWaisen = self::hole();
		Scope::setBackendSessionValue(self::SITZUNGSSCHLUESSEL, array());

		if ($arrWaisen && !Input::get('verwerfen'))
		{
			foreach ($arrWaisen as $intId => $strBeschreibung)
			{
				Database::getInstance()->prepare('DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE id = ?')
				                        ->execute($intId);

				Scope::log(
					'[Fernschach-Verwaltung] Nenngeldbuchung '.$intId.' ('.$strBeschreibung.') nach Rückfrage im Backend gelöscht.',
					__METHOD__,
					ContaoContext::GENERAL
				);
			}

			Message::addConfirmation(1 === \count($arrWaisen) ? 'Die Nenngeldbuchung wurde gelöscht.' : \count($arrWaisen).' Nenngeldbuchungen wurden gelöscht.');
		}

		// Ohne Umleitung bliebe key= in der Adresse stehen; ein Aktualisieren der
		// Seite liefe dann erneut in diese Methode.
		Controller::redirect(self::addToUrl('', true, array('key', 'verwerfen')));
	}

	/**
	 * Liest die gemerkten Buchungen aus der Backend-Sitzung.
	 *
	 * @return array Buchungsnummer => Beschreibung; leer, wenn nichts vorliegt
	 */
	private static function hole(): array
	{
		$arrWaisen = Scope::getBackendSessionValue(self::SITZUNGSSCHLUESSEL, array());

		return \is_array($arrWaisen) ? $arrWaisen : array();
	}

	/**
	 * Ermittelt die Spielernummer zu einer Anmeldung oder Bewerbung.
	 *
	 * Bevorzugt wird die unmittelbar hinterlegte Spielernummer. Fehlt sie, wird
	 * über die BdF-Mitgliedsnummer gesucht — so sind alte Datensätze angelegt,
	 * bei denen die Zuordnung erst später erfolgte.
	 *
	 * @param object $objDatensatz Der Datensatz mit spielerId bzw. memberId
	 *
	 * @return int Die Spielernummer aus tl_fernschach_spieler; 0, wenn keine zu
	 *             ermitteln ist
	 */
	private static function ermittleSpieler($objDatensatz): int
	{
		if ($objDatensatz->spielerId)
		{
			return (int) $objDatensatz->spielerId;
		}

		if (!$objDatensatz->memberId)
		{
			return 0;
		}

		$objSpieler = Helper::getSpielerdatensatz(null, $objDatensatz->memberId);

		return ($objSpieler && $objSpieler->numRows) ? (int) $objSpieler->id : 0;
	}

	/**
	 * Beschreibt eine Buchung so, dass sie im Hinweis wiederzuerkennen ist.
	 *
	 * @param object $objBuchung Datensatz aus tl_fernschach_spieler_konto_nenngeld
	 *
	 * @return string Datum, Betrag und Verwendungszweck in einer Zeile
	 */
	private static function beschreibe($objBuchung): string
	{
		return ($objBuchung->datum ? date('d.m.Y', (int) $objBuchung->datum) : 'ohne Datum')
			.' | '.number_format((float) $objBuchung->betrag, 2, ',', '.').' €'
			.' | '.($objBuchung->verwendungszweck ?: 'ohne Verwendungszweck');
	}
}
