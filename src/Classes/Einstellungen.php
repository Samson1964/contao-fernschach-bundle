<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Config;
use Contao\StringUtil;

/**
 * Liest die Einstellungen der Fernschach-Verwaltung aus.
 *
 * Bisher stand hier nur die Auswahl, welche Prüfungen eine Turnieranmeldung zu
 * bestehen hat. Sie war jahrelang mit dem Zusatz „noch nicht implementiert"
 * beschriftet: Die Prüfungen selbst gab es, ausgewertet wurde das Feld aber
 * nicht. Seit Version 2.12.0 richten sich die Meldeformulare danach.
 */
class Einstellungen
{
	/**
	 * Beitragskonto ausgeglichen oder SEPA-Mandat Beitrag vorhanden.
	 */
	public const PRUEFUNG_BEITRAG = '1';

	/**
	 * Im Januar den Stand vom 31.12. des Vorjahres heranziehen.
	 */
	public const PRUEFUNG_JANUAR = '2';

	/**
	 * Nenngeld gedeckt oder SEPA-Mandat Nenngeld vorhanden.
	 */
	public const PRUEFUNG_NENNGELD = '3';

	/**
	 * Liefert die Prüfungen, die eine Turnieranmeldung bestehen muss.
	 *
	 * Ist in den Einstellungen nichts ausgewählt, gelten **alle** Prüfungen.
	 * Das ist bewusst so herum: Auf einer bestehenden Installation wurde das
	 * Feld nie gefüllt, weil es nie ausgewertet wurde. Eine leere Auswahl als
	 * „gar keine Prüfung" zu lesen, hätte beim Update sämtliche Sperren
	 * stillschweigend aufgehoben.
	 *
	 * @return array Die Kennungen der aktiven Prüfungen, siehe die Konstanten
	 *               dieser Klasse
	 */
	public static function pruefungen()
	{
		$arrPruefungen = StringUtil::deserialize(Config::get('fernschach_check_turnieranmeldung'), true);
		$arrPruefungen = array_filter(array_map('strval', $arrPruefungen), static function ($strWert) { return '' !== $strWert; });

		if (!$arrPruefungen)
		{
			return array(self::PRUEFUNG_BEITRAG, self::PRUEFUNG_JANUAR, self::PRUEFUNG_NENNGELD);
		}

		return array_values($arrPruefungen);
	}

	/**
	 * Prüft, ob eine bestimmte Prüfung eingeschaltet ist.
	 *
	 * @param string $strPruefung Kennung der Prüfung, siehe die Konstanten dieser Klasse
	 *
	 * @return bool True, wenn die Prüfung anzuwenden ist
	 */
	public static function pruefungAktiv($strPruefung)
	{
		return \in_array((string) $strPruefung, self::pruefungen(), true);
	}
}
