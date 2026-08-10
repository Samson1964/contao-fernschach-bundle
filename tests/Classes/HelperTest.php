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
}
