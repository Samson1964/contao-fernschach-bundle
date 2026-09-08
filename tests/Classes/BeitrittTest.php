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
use Schachbulle\ContaoFernschachBundle\Classes\Beitritt;

/**
 * Prüft die Beitrittserklärung, soweit sie ohne Datenbank auskommt:
 * die Monatsauswahl, die Datumserkennung, die Prüfung der Eingaben und den
 * Fließtext für das Feld *Informationen zum Beitritt*.
 */
class BeitrittTest extends TestCase
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
	 * Liefert eine vollständig und richtig ausgefüllte Erklärung.
	 *
	 * @param int $intZeitpunkt Stichtag, aus dem der angebotene Beitrittsmonat stammt
	 *
	 * @return array Die Werte, wie sie das Formular schicken würde
	 */
	private function erklaerung(int $intZeitpunkt): array
	{
		return array
		(
			'vorname'             => 'Max',
			'nachname'            => 'Mustermann',
			'strasse'             => 'Musterweg 1',
			'plz'                 => '12345',
			'ort'                 => 'Musterstadt',
			'geburtstag'          => '24.03.1968',
			'staat'               => 'deutsch',
			'telefon'             => '030 1234567',
			'email'               => 'max@example.org',
			'bdf_mitglied'        => 'Nein',
			'mitgliedsnummer'     => '',
			'fernschach_erfolge'  => '',
			'nahschach_erfolge'   => 'Kreismeister 1998',
			'elo'                 => '1850',
			'dwz'                 => '',
			'beitrittsmonat'      => array_key_first(Beitritt::monate(4, $intZeitpunkt)),
			'beitrittszustimmung' => 'ja',
			'datenschutz'         => 'ja',
		);
	}

	/**
	 * Die Monatsauswahl beginnt beim laufenden Monat und läuft über den
	 * Jahreswechsel weiter.
	 *
	 * @return void
	 */
	public function testMonatsauswahlLaeuftUeberDenJahreswechsel(): void
	{
		$arrMonate = Beitritt::monate(4, mktime(12, 0, 0, 11, 20, 2026));

		// Die Schlüssel entstehen als Zeichenketten '202611'; PHP macht daraus
		// beim Ablegen im Array selbsttätig Zahlen. Für die Suche macht das
		// keinen Unterschied — isset($arrMonate['202611']) findet den Eintrag —,
		// beim Vergleich mit === aber sehr wohl
		$this->assertSame(array(202611, 202612, 202701, 202702), array_keys($arrMonate));
		$this->assertTrue(isset($arrMonate['202611']), 'Der Zugriff über die Zeichenkette muss trotzdem gehen.');
		$this->assertSame('1. November 2026', $arrMonate['202611']);
		$this->assertSame('1. Januar 2027', $arrMonate['202701']);
	}

	/**
	 * Ein Datum wird in beiden gängigen Schreibweisen gelesen; was es nicht
	 * gibt, wird abgewiesen.
	 *
	 * @return void
	 */
	public function testDatumWirdErkannt(): void
	{
		$this->assertSame(19680324, Beitritt::datum('24.03.1968'));
		$this->assertSame(19680324, Beitritt::datum('24.3.1968'));
		$this->assertSame(19680324, Beitritt::datum('1968-03-24'));

		$this->assertSame(0, Beitritt::datum('31.02.1968'), 'Den 31. Februar gibt es nicht.');
		$this->assertSame(0, Beitritt::datum('24.03.68'));
		$this->assertSame(0, Beitritt::datum('irgendwas'));
		$this->assertSame(0, Beitritt::datum(''));
	}

	/**
	 * Eine vollständige Erklärung geht ohne Beanstandung durch.
	 *
	 * @return void
	 */
	public function testVollstaendigeErklaerungIstFehlerfrei(): void
	{
		$intZeitpunkt = mktime(12, 0, 0, 5, 10, 2026);

		$this->assertSame(array(), Beitritt::pruefe($this->erklaerung($intZeitpunkt), $intZeitpunkt));
	}

	/**
	 * Jedes Pflichtfeld wird auch verlangt.
	 *
	 * @return void
	 */
	public function testPflichtfelderWerdenVerlangt(): void
	{
		$arrFehler = Beitritt::pruefe(array());

		foreach (Beitritt::felder() as $strName => $arrFeld)
		{
			if (empty($arrFeld['pflicht']))
			{
				$this->assertArrayNotHasKey($strName, $arrFehler, $strName.' ist freiwillig und darf nicht bemängelt werden.');

				continue;
			}

			$this->assertArrayHasKey($strName, $arrFehler, $strName.' ist Pflicht und muss bemängelt werden.');
		}

		// Ohne die beiden Häkchen geht es nicht
		$this->assertStringContainsString('Zustimmung', $arrFehler['beitrittszustimmung']);
		$this->assertStringContainsString('Zustimmung', $arrFehler['datenschutz']);
	}

	/**
	 * Ein unbrauchbares Geburtsdatum wird abgewiesen — auch eines aus der
	 * Zukunft.
	 *
	 * @return void
	 */
	public function testGeburtsdatumWirdGeprueft(): void
	{
		$intZeitpunkt = mktime(12, 0, 0, 5, 10, 2026);
		$arrWerte = $this->erklaerung($intZeitpunkt);

		$arrWerte['geburtstag'] = '31.02.1968';
		$this->assertArrayHasKey('geburtstag', Beitritt::pruefe($arrWerte, $intZeitpunkt));

		$arrWerte['geburtstag'] = date('d.m.', time() + 86400 * 2).(int) date('Y');
		$this->assertArrayHasKey('geburtstag', Beitritt::pruefe($arrWerte, $intZeitpunkt));

		$arrWerte['geburtstag'] = '01.01.1800';
		$this->assertArrayHasKey('geburtstag', Beitritt::pruefe($arrWerte, $intZeitpunkt));
	}

	/**
	 * Auswahlfelder nehmen nur an, was auch angeboten wurde.
	 *
	 * Ein Browser lässt sich in zehn Sekunden dazu überreden, etwas anderes zu
	 * schicken; geprüft wird deshalb auf dem Server.
	 *
	 * @return void
	 */
	public function testNurAngeboteneWerteWerdenAngenommen(): void
	{
		$intZeitpunkt = mktime(12, 0, 0, 5, 10, 2026);
		$arrWerte = $this->erklaerung($intZeitpunkt);

		$arrWerte['bdf_mitglied'] = 'Vielleicht';
		$this->assertArrayHasKey('bdf_mitglied', Beitritt::pruefe($arrWerte, $intZeitpunkt));

		$arrWerte = $this->erklaerung($intZeitpunkt);
		$arrWerte['beitrittsmonat'] = '190001';
		$this->assertArrayHasKey('beitrittsmonat', Beitritt::pruefe($arrWerte, $intZeitpunkt));
	}

	/**
	 * E-Mail-Adresse und Wertungszahlen werden auf Plausibilität geprüft.
	 *
	 * @return void
	 */
	public function testAdresseUndWertungszahlen(): void
	{
		$intZeitpunkt = mktime(12, 0, 0, 5, 10, 2026);
		$arrWerte = $this->erklaerung($intZeitpunkt);

		$arrWerte['email'] = 'keine adresse';
		$this->assertArrayHasKey('email', Beitritt::pruefe($arrWerte, $intZeitpunkt));

		$arrWerte = $this->erklaerung($intZeitpunkt);
		$arrWerte['dwz'] = '99';
		$this->assertArrayHasKey('dwz', Beitritt::pruefe($arrWerte, $intZeitpunkt));

		$arrWerte['dwz'] = '1500';
		$this->assertSame(array(), Beitritt::pruefe($arrWerte, $intZeitpunkt));

		// Leer bleiben darf beides
		$arrWerte['dwz'] = '';
		$arrWerte['elo'] = '';
		$arrWerte['email'] = '';
		$this->assertSame(array(), Beitritt::pruefe($arrWerte, $intZeitpunkt));
	}

	/**
	 * Im Fließtext steht alles, was keine eigene Spalte hat — und nichts, was
	 * ohnehin im Datensatz landet.
	 *
	 * @return void
	 */
	public function testFliesstextEnthaeltNurDasUebrige(): void
	{
		$intZeitpunkt = mktime(12, 0, 0, 5, 10, 2026);
		$strText = Beitritt::infotext($this->erklaerung($intZeitpunkt));

		$this->assertStringContainsString('Staatsangehörigkeit: deutsch', $strText);
		$this->assertStringContainsString('Nahschach-Elo: 1850', $strText);
		$this->assertStringContainsString('Bereits BdF-Mitglied gewesen?: Nein', $strText);
		$this->assertStringContainsString('Eingegangen am:', $strText);

		// Der Name steht in seiner eigenen Spalte und gehört nicht in den Text
		$this->assertStringNotContainsString('Mustermann', $strText);

		// Leere Felder erzeugen keine leeren Zeilen
		$this->assertStringNotContainsString('Nahschach-DWZ', $strText);
	}
}
