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
use Schachbulle\ContaoFernschachBundle\Classes\Einstellungen;
use Schachbulle\ContaoFernschachBundle\Classes\Helper;

/**
 * Prüft, wie die Einstellung *Prüfungen bei Turnieranmeldungen* wirkt.
 *
 * Config::get() liest aus $GLOBALS['TL_CONFIG'], deshalb lässt sich die
 * Einstellung hier ohne gebootetes Contao setzen.
 */
class EinstellungenTest extends TestCase
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

		unset($GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung']);
	}

	/**
	 * Räumt die Einstellung wieder weg.
	 *
	 * Sie steht in einer globalen Variablen und überlebte sonst diesen Test:
	 * Die Prüfungen in HelperTest liefen dann unter einer Einstellung, die sie
	 * gar nicht gesetzt haben.
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		unset($GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung']);
	}

	/**
	 * Baut einen Spielerdatensatz mit den beiden SEPA-Feldern.
	 *
	 * @param bool $blnBeitrag  SEPA-Mandat für den Beitrag vorhanden
	 * @param bool $blnNenngeld SEPA-Mandat für das Nenngeld vorhanden
	 *
	 * @return object Objekt mit id, sepaBeitrag und sepaNenngeld
	 */
	private function spieler(bool $blnBeitrag, bool $blnNenngeld): object
	{
		$objSpieler = new \stdClass();
		$objSpieler->id = 900001;
		$objSpieler->sepaBeitrag = $blnBeitrag ? '1' : '';
		$objSpieler->sepaNenngeld = $blnNenngeld ? '1' : '';

		return $objSpieler;
	}

	/**
	 * Ohne Auswahl gelten alle Prüfungen.
	 *
	 * Das Feld wurde jahrelang nicht ausgewertet und ist auf einer bestehenden
	 * Installation deshalb leer. Eine leere Auswahl als „gar keine Prüfung" zu
	 * lesen, hätte beim Update sämtliche Sperren stillschweigend aufgehoben.
	 *
	 * @return void
	 */
	public function testOhneAuswahlGeltenAllePruefungen(): void
	{
		$this->assertSame(array('1', '2', '3'), Einstellungen::pruefungen());

		$GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung'] = serialize(array());
		$this->assertSame(array('1', '2', '3'), Einstellungen::pruefungen());

		$GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung'] = '';
		$this->assertSame(array('1', '2', '3'), Einstellungen::pruefungen());
	}

	/**
	 * Eine getroffene Auswahl gilt genau so, wie sie dasteht.
	 *
	 * @return void
	 */
	public function testAuswahlGiltGenauSo(): void
	{
		$GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung'] = serialize(array('1', '3'));

		$this->assertTrue(Einstellungen::pruefungAktiv(Einstellungen::PRUEFUNG_BEITRAG));
		$this->assertFalse(Einstellungen::pruefungAktiv(Einstellungen::PRUEFUNG_JANUAR));
		$this->assertTrue(Einstellungen::pruefungAktiv(Einstellungen::PRUEFUNG_NENNGELD));
	}

	/**
	 * Ist die Nenngeldprüfung abgewählt, steht das Nenngeld einer Meldung nicht
	 * mehr im Weg.
	 *
	 * @return void
	 */
	public function testAbgewaehlteNenngeldpruefungLaesstAllesDurch(): void
	{
		$objSpieler = $this->spieler(false, false);

		// Mit Prüfung: 5,00 € Nenngeld bei 2,00 € Guthaben geht nicht
		$this->assertFalse(Helper::nenngeldGedeckt($objSpieler, 5.0, 2.0));

		$GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung'] = serialize(array('1'));
		$this->assertTrue(Helper::nenngeldGedeckt($objSpieler, 5.0, 2.0));
	}

	/**
	 * Ein SEPA-Mandat für das Nenngeld reicht weiterhin aus.
	 *
	 * @return void
	 */
	public function testSepaMandatDecktDasNenngeld(): void
	{
		$this->assertTrue(Helper::nenngeldGedeckt($this->spieler(false, true), 5.0, -100.0));
	}

	/**
	 * Ist die Beitragsprüfung abgewählt, steht der Beitrag einer Meldung nicht
	 * mehr im Weg — ohne dass dafür die Datenbank befragt würde.
	 *
	 * @return void
	 */
	public function testAbgewaehlteBeitragspruefungLaesstAllesDurch(): void
	{
		$GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung'] = serialize(array('3'));

		$this->assertTrue(Helper::beitragGedeckt($this->spieler(false, false)));
	}

	/**
	 * Ohne Spielerdatensatz bleibt es bei einer Absage, auch wenn die Prüfung
	 * abgewählt ist.
	 *
	 * @return void
	 */
	public function testOhneSpielerBleibtEsBeiDerAbsage(): void
	{
		$GLOBALS['TL_CONFIG']['fernschach_check_turnieranmeldung'] = serialize(array('3'));

		$objLeer = new \stdClass();
		$objLeer->id = 0;

		$this->assertFalse(Helper::beitragGedeckt($objLeer));
		$this->assertFalse(Helper::beitragGedeckt(null));
	}
}
