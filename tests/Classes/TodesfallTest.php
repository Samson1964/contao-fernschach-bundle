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
use Schachbulle\ContaoFernschachBundle\Classes\Todesfall;

/**
 * Prüft, wie der Todestag in die Mitgliedschaften eines Spielers einzieht.
 *
 * Getestet wird die Regel selbst, die ohne Datenbank auskommt. Der Cronjob, der
 * sie anwendet, wird über die Testinstallationen geprüft.
 */
class TodesfallTest extends TestCase
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
	 * Eine laufende Mitgliedschaft endet mit dem Todestag.
	 *
	 * @return void
	 */
	public function testLaufendeMitgliedschaftEndetMitDemTodestag(): void
	{
		$arrAlt = array(array('from' => '20100101', 'to' => '0', 'status' => ''));

		$arrNeu = Todesfall::beendeMitgliedschaft(serialize($arrAlt), 20260315);

		$this->assertSame('20260315', $arrNeu[0]['to']);
		$this->assertSame('Verstorben', $arrNeu[0]['status']);
		$this->assertSame('20100101', $arrNeu[0]['from'], 'Der Beginn darf nicht angefasst werden.');
	}

	/**
	 * Eine bereits beendete Mitgliedschaft bleibt, wie sie ist.
	 *
	 * Wer Jahre nach seinem Austritt stirbt, war zum Todeszeitpunkt kein
	 * Mitglied mehr. Eine Mitgliedschaft anzuhängen oder das alte Ende zu
	 * überschreiben wäre schlicht falsch.
	 *
	 * @return void
	 */
	public function testBeendeteMitgliedschaftBleibtUnberuehrt(): void
	{
		$arrAlt = array(array('from' => '20100101', 'to' => '20201231', 'status' => 'Streichung'));

		$this->assertNull(Todesfall::beendeMitgliedschaft(serialize($arrAlt), 20260315));
	}

	/**
	 * Ein zweiter Lauf ändert nichts mehr.
	 *
	 * Sonst würde der tägliche Cronjob bei jedem Lauf dieselbe Änderung melden.
	 *
	 * @return void
	 */
	public function testZweiterLaufAendertNichts(): void
	{
		$arrAlt = array(array('from' => '20100101', 'to' => '0', 'status' => ''));

		$arrNeu = Todesfall::beendeMitgliedschaft(serialize($arrAlt), 20260315);

		$this->assertNull(Todesfall::beendeMitgliedschaft(serialize($arrNeu), 20260315));
	}

	/**
	 * Steht das Ende punktiert im Bestand, gilt die Mitgliedschaft als beendet.
	 *
	 * Ein einfacher Vergleich mit 0 würde '31.12.2020' für offen halten und das
	 * Ende überschreiben.
	 *
	 * @return void
	 */
	public function testPunktiertesEndeGiltAlsBeendet(): void
	{
		$arrAlt = array(array('from' => '01.01.2010', 'to' => '31.12.2020', 'status' => 'Streichung'));

		$this->assertNull(Todesfall::beendeMitgliedschaft(serialize($arrAlt), 20260315));
	}

	/**
	 * Von mehreren Mitgliedschaften wird nur die offene beendet.
	 *
	 * @return void
	 */
	public function testNurDieOffeneMitgliedschaftWirdBeendet(): void
	{
		$arrAlt = array(
			array('from' => '20000101', 'to' => '20051231', 'status' => 'Austritt'),
			array('from' => '20100101', 'to' => '0', 'status' => ''),
		);

		$arrNeu = Todesfall::beendeMitgliedschaft(serialize($arrAlt), 20260315);

		$this->assertSame('20051231', $arrNeu[0]['to']);
		$this->assertSame('Austritt', $arrNeu[0]['status']);
		$this->assertSame('20260315', $arrNeu[1]['to']);
		$this->assertSame('Verstorben', $arrNeu[1]['status']);
	}

	/**
	 * Ohne Todestag und ohne Mitgliedschaften passiert nichts.
	 *
	 * @return void
	 */
	public function testOhneAngabenPassiertNichts(): void
	{
		$arrAlt = array(array('from' => '20100101', 'to' => '0', 'status' => ''));

		$this->assertNull(Todesfall::beendeMitgliedschaft(serialize($arrAlt), 0));
		$this->assertNull(Todesfall::beendeMitgliedschaft(serialize(array()), 20260315));
		$this->assertNull(Todesfall::beendeMitgliedschaft('', 20260315));
		$this->assertNull(Todesfall::beendeMitgliedschaft(null, 20260315));
	}
}
