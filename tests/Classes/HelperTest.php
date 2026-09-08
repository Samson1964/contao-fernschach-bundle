<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Tests\Classes;

use Contao\Backend;
use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoFernschachBundle\Classes\Helper;

/**
 * Prüft die Begrenzung der Meldungen je Spieler und Turnier.
 *
 * Getestet werden die Zweige, die ohne Datenbank auskommen: die Sonderfälle
 * „kein Turnier", „kein Spieler" und „unbegrenzt". Die zählenden Zweige brauchen
 * eine Contao-Installation und werden über die Testinstallationen geprüft.
 */
class HelperTest extends TestCase
{
	/**
	 * Überspringt die Tests, wenn die Contao-Klassen nicht erreichbar sind.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		if (!class_exists(Backend::class))
		{
			$this->markTestSkipped('Ohne Contao-Autoloader (CONTAO_AUTOLOAD) sind diese Tests nicht ausführbar.');
		}
	}

	/**
	 * Baut einen Turnierdatensatz, wie ihn Contao\Database\Result liefern würde.
	 *
	 * @param int $intMax Wert des Feldes maxMeldungen
	 *
	 * @return object Objekt mit den Feldern id und maxMeldungen
	 */
	private function turnier(int $intMax): object
	{
		$objTurnier = new \stdClass();
		$objTurnier->id = 42;
		$objTurnier->maxMeldungen = $intMax;

		return $objTurnier;
	}

	/**
	 * Ohne Turnier oder ohne Spieler gibt es nichts zuzuordnen — dann darf auch
	 * nicht gemeldet werden.
	 *
	 * @return void
	 */
	public function testOhneTurnierOderSpielerNichtErlaubt(): void
	{
		$this->assertFalse(Helper::meldungErlaubt(null, 1));
		$this->assertFalse(Helper::meldungErlaubt(false, 1));
		$this->assertFalse(Helper::meldungErlaubt($this->turnier(1), 0));
		$this->assertFalse(Helper::meldungErlaubt($this->turnier(1), null));
	}

	/**
	 * Der Wert 0 im Feld maxMeldungen bedeutet „unbegrenzt". Die Datenbank wird
	 * dann gar nicht erst befragt.
	 *
	 * @return void
	 */
	public function testNullBedeutetUnbegrenzt(): void
	{
		$this->assertTrue(Helper::meldungErlaubt($this->turnier(0), 7));
		$this->assertTrue(Helper::meldungErlaubt($this->turnier(0), 7, true));
	}

	/**
	 * Ein fehlendes Feld maxMeldungen (etwa bei einem alten Datensatz aus einem
	 * Test) darf nicht zu einem Fehler führen, sondern gilt als unbegrenzt.
	 *
	 * @return void
	 */
	public function testFehlendesFeldGiltAlsUnbegrenzt(): void
	{
		$objTurnier = new \stdClass();
		$objTurnier->id = 42;

		$this->assertTrue(Helper::meldungErlaubt($objTurnier, 7));
	}

	/**
	 * Ohne IDs zählt die Abfrage nichts und fasst die Datenbank nicht an.
	 *
	 * @return void
	 */
	public function testZaehlenOhneIdsErgibtNull(): void
	{
		$this->assertSame(0, Helper::zaehleMeldungen(0, 0));
		$this->assertSame(0, Helper::zaehleMeldungen(42, 0));
		$this->assertSame(0, Helper::zaehleMeldungen(0, 7));
	}

	/**
	 * Ohne Spieler-ID gibt es kein Konto im internen Bereich.
	 *
	 * @return void
	 */
	public function testInternerBereichOhneSpieler(): void
	{
		$this->assertSame('Nein', Helper::getInternerBereich(0));
		$this->assertSame('Nein', Helper::getInternerBereich(null));
	}

	/**
	 * Mit SEPA-Vereinbarung ist das Nenngeld immer gedeckt.
	 *
	 * Der Kontostand spielt dann keine Rolle, weil abgebucht wird. Auch ein
	 * negativer Stand hindert die Meldung nicht.
	 *
	 * @return void
	 */
	public function testMitSepaIstDasNenngeldImmerGedeckt(): void
	{
		$objSpieler = new \stdClass();
		$objSpieler->id = 7;
		$objSpieler->sepaNenngeld = '1';

		$this->assertTrue(Helper::nenngeldGedeckt($objSpieler, 25.50, 0.0));
		$this->assertTrue(Helper::nenngeldGedeckt($objSpieler, 25.50, -100.0));
	}

	/**
	 * Ohne SEPA-Vereinbarung entscheidet das Guthaben, auf den Cent genau.
	 *
	 * Der frühere Weg hat das Nenngeld mit (int) auf ganze Euro gekürzt — aus
	 * 25,50 wurde 25, und mit 25,49 auf dem Konto war die Meldung fälschlich
	 * möglich. Genau diese Grenze wird hier festgehalten.
	 *
	 * @return void
	 */
	public function testOhneSepaEntscheidetDasGuthabenAufDenCent(): void
	{
		$objSpieler = new \stdClass();
		$objSpieler->id = 7;
		$objSpieler->sepaNenngeld = '';

		$this->assertFalse(Helper::nenngeldGedeckt($objSpieler, 25.50, 0.0));
		$this->assertFalse(Helper::nenngeldGedeckt($objSpieler, 25.50, 25.49));
		$this->assertTrue(Helper::nenngeldGedeckt($objSpieler, 25.50, 25.50));
		$this->assertTrue(Helper::nenngeldGedeckt($objSpieler, 25.50, 100.0));
	}

	/**
	 * Ein Nenngeld von 0 ist auch mit leerem Konto gedeckt.
	 *
	 * Turniere ohne Nenngeld gibt es; sie dürfen an der Prüfung nicht scheitern.
	 *
	 * @return void
	 */
	public function testNenngeldNullIstImmerGedeckt(): void
	{
		$objSpieler = new \stdClass();
		$objSpieler->id = 7;
		$objSpieler->sepaNenngeld = '';

		$this->assertTrue(Helper::nenngeldGedeckt($objSpieler, 0, 0.0));
		$this->assertFalse(Helper::nenngeldGedeckt($objSpieler, 0.01, 0.0));
	}

	/**
	 * Ein Datum aus einer Mitgliedschaft wird immer als JJJJMMTT gelesen.
	 *
	 * Gespeichert wird JJJJMMTT, im Bestand stehen aber auch punktierte Angaben
	 * aus der Anzeigeform. Beides muss dieselbe Zahl ergeben.
	 *
	 * @return void
	 */
	public function testMitgliedschaftsdatumLiestBeideSchreibweisen(): void
	{
		$this->assertSame(20251231, Helper::mitgliedschaftsdatum('20251231'));
		$this->assertSame(20251231, Helper::mitgliedschaftsdatum('31.12.2025'));
		$this->assertSame(20251231, Helper::mitgliedschaftsdatum(20251231));
	}

	/**
	 * Unvollständige Angaben werden mit Nullen aufgefüllt.
	 *
	 * @return void
	 */
	public function testMitgliedschaftsdatumFuelltUnvollstaendigeAngabenAuf(): void
	{
		$this->assertSame(20251200, Helper::mitgliedschaftsdatum('202512'));
		$this->assertSame(20251200, Helper::mitgliedschaftsdatum('12.2025'));
		$this->assertSame(20250000, Helper::mitgliedschaftsdatum('2025'));
	}

	/**
	 * Leere und unlesbare Angaben ergeben 0.
	 *
	 * @return void
	 */
	public function testMitgliedschaftsdatumOhneWert(): void
	{
		$this->assertSame(0, Helper::mitgliedschaftsdatum(''));
		$this->assertSame(0, Helper::mitgliedschaftsdatum(0));
		$this->assertSame(0, Helper::mitgliedschaftsdatum(null));
		$this->assertSame(0, Helper::mitgliedschaftsdatum('Unsinn'));
	}

	/**
	 * Eine beendete Mitgliedschaft gilt auch dann als beendet, wenn die Daten
	 * punktiert gespeichert sind.
	 *
	 * Genau hier lag der Fehler: PHP 8 vergleicht '31.12.2025' mit 20260831 als
	 * Zeichenketten, und '3' ist größer als '2'. Die Mitgliedschaft galt damit
	 * als aktiv, obwohl sie seit Monaten beendet war.
	 *
	 * @return void
	 */
	public function testBeendeteMitgliedschaftMitPunktiertenDaten(): void
	{
		$objSpieler = new \stdClass();
		$objSpieler->id = 424;
		$objSpieler->nachname = 'Tagsold';
		$objSpieler->vorname = 'Christian';
		$objSpieler->memberId = 11111;
		$objSpieler->death = '';
		$objSpieler->isDeletion = '';
		$objSpieler->streichung = 0;
		$objSpieler->memberships = serialize(array(array('from' => '01.06.2015', 'to' => '31.12.2025', 'status' => 'Streichung')));

		$this->assertFalse(Helper::checkMembership($objSpieler, 20260831));

		// Zum Gegenbeweis dieselbe Mitgliedschaft numerisch
		$objSpieler->memberships = serialize(array(array('from' => '20150601', 'to' => '20251231', 'status' => 'Streichung')));
		$this->assertFalse(Helper::checkMembership($objSpieler, 20260831));

		// Und eine laufende Mitgliedschaft, punktiert gespeichert
		$objSpieler->memberships = serialize(array(array('from' => '01.06.2015', 'to' => 0, 'status' => '')));
		$this->assertTrue(Helper::checkMembership($objSpieler, 20260831));
	}

	/**
	 * Setzt die beiden Mitgliedergruppen aus den Einstellungen.
	 *
	 * Config::get() liest aus $GLOBALS['TL_CONFIG'], deshalb reicht es, die
	 * Werte dort zu hinterlegen — eine gebootete Contao-Installation ist dafür
	 * nicht nötig.
	 *
	 * @param string $strStandard ID der Standard-Mitgliedergruppe, '' = nicht gesetzt
	 * @param string $strBdF      ID der BdF-Mitgliedergruppe, '' = nicht gesetzt
	 *
	 * @return void
	 */
	private function gruppenEinstellen(string $strStandard, string $strBdF): void
	{
		$GLOBALS['TL_CONFIG']['fernschach_memberDefault'] = $strStandard;
		$GLOBALS['TL_CONFIG']['fernschach_memberFernschach'] = $strBdF;
	}

	/**
	 * Die Standardgruppe bleibt einem BdF-Mitglied erhalten.
	 *
	 * Bis Version 2.10.1 tauschte die Wartung die beiden Gruppen gegeneinander
	 * aus. Ein Mitglied verlor damit alle Rechte, die an der Standardgruppe
	 * hingen, sobald es in den BdF eintrat — und bekam sie beim Austritt
	 * zurück. Der Bot schob die Konten so zwischen beiden Gruppen hin und her.
	 *
	 * @return void
	 */
	public function testStandardgruppeBleibtBeiBdfMitgliedern(): void
	{
		$this->gruppenEinstellen('2', '5');

		$strGruppen = Helper::mitgliedergruppen(serialize(array('2')), true);

		$this->assertSame(array('2', '5'), \Contao\StringUtil::deserialize($strGruppen, true));
	}

	/**
	 * Beim Austritt fällt nur die BdF-Gruppe weg, die Standardgruppe bleibt.
	 *
	 * @return void
	 */
	public function testBeimAustrittBleibtNurDieStandardgruppe(): void
	{
		$this->gruppenEinstellen('2', '5');

		$strGruppen = Helper::mitgliedergruppen(serialize(array('2', '5')), false);

		$this->assertSame(array('2'), \Contao\StringUtil::deserialize($strGruppen, true));
	}

	/**
	 * Fehlt die Standardgruppe im Konto, wird sie nachgetragen — auch beim
	 * BdF-Mitglied.
	 *
	 * @return void
	 */
	public function testFehlendeStandardgruppeWirdNachgetragen(): void
	{
		$this->gruppenEinstellen('2', '5');

		$strGruppen = Helper::mitgliedergruppen(serialize(array()), true);

		$this->assertSame(array('2', '5'), \Contao\StringUtil::deserialize($strGruppen, true));
	}

	/**
	 * Von Hand vergebene Gruppen bleiben unangetastet.
	 *
	 * Hier lag ein zweiter Fehler: array_search() liefert false, wenn die
	 * gesuchte Gruppe fehlt, und isset(false) ist wahr. Die alte Fassung
	 * löschte damit den Schlüssel 0 — also eine völlig fremde Gruppe.
	 *
	 * @return void
	 */
	public function testFremdeGruppenBleibenErhalten(): void
	{
		$this->gruppenEinstellen('2', '5');

		$strGruppen = Helper::mitgliedergruppen(serialize(array('9', '2')), true);
		$this->assertSame(array('9', '2', '5'), \Contao\StringUtil::deserialize($strGruppen, true));

		$strGruppen = Helper::mitgliedergruppen(serialize(array('9', '2')), false);
		$this->assertSame(array('9', '2'), \Contao\StringUtil::deserialize($strGruppen, true));
	}

	/**
	 * Ist eine der Gruppen nicht eingestellt, wird sie weder eingetragen noch
	 * entfernt.
	 *
	 * @return void
	 */
	public function testNichtEingestellteGruppenBleibenAussenVor(): void
	{
		$this->gruppenEinstellen('', '');

		$strGruppen = Helper::mitgliedergruppen(serialize(array('9')), true);
		$this->assertSame(array('9'), \Contao\StringUtil::deserialize($strGruppen, true));

		$strGruppen = Helper::mitgliedergruppen(serialize(array('9')), false);
		$this->assertSame(array('9'), \Contao\StringUtil::deserialize($strGruppen, true));
	}

	/**
	 * Zweimal hintereinander aufgerufen kommt dasselbe heraus — sonst würde die
	 * Wartung bei jedem Lauf eine Änderung melden und eine Version anlegen.
	 *
	 * @return void
	 */
	public function testZweiterLaufAendertNichts(): void
	{
		$this->gruppenEinstellen('2', '5');

		$strErster = Helper::mitgliedergruppen(serialize(array('2')), true);
		$strZweiter = Helper::mitgliedergruppen($strErster, true);

		$this->assertSame($strErster, $strZweiter);
	}
}