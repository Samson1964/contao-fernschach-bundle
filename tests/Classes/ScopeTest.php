<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Tests\Classes;

use Contao\System;
use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

/**
 * Prüft die Rückfallwege von Scope.
 *
 * Die Klasse ersetzt Konstanten und Klassen, die es in Contao 5 nicht mehr gibt.
 * Wichtig ist dabei vor allem, dass sie ohne laufende Anfrage — also im Cronjob
 * und auf der Kommandozeile — nichts kaputt macht, sondern harmlose Werte
 * liefert.
 */
class ScopeTest extends TestCase
{
	/**
	 * Überspringt die Tests, wenn die Contao-Klassen nicht erreichbar sind.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		if (!class_exists(System::class))
		{
			$this->markTestSkipped('Ohne Contao-Autoloader (CONTAO_AUTOLOAD) sind diese Tests nicht ausführbar.');
		}
	}

	/**
	 * Ohne Container gibt es keine Anfrage — und damit auch kein Backend.
	 *
	 * @return void
	 */
	public function testOhneContainerKeinBackend(): void
	{
		$this->assertFalse(Scope::isBackendRequest());
	}

	/**
	 * Ohne Container gibt es keinen Token; eine leere Zeichenkette ist besser
	 * als ein Fehler, weil der Rückgabewert unmittelbar in Links landet.
	 *
	 * @return void
	 */
	public function testOhneContainerLeererToken(): void
	{
		$this->assertSame('', Scope::getRequestToken());
	}

	/**
	 * Ohne Container ist die Backend-Sitzung leer, aber ein Feld — sonst würde
	 * jeder lesende Zugriff der Aufrufer scheitern.
	 *
	 * @return void
	 */
	public function testOhneContainerLeereSitzung(): void
	{
		$this->assertSame(array(), Scope::getBackendSession());
		$this->assertNull(Scope::getBackendSessionValue('irgendwas'));
		$this->assertSame('Standard', Scope::getBackendSessionValue('irgendwas', 'Standard'));
	}

	/**
	 * Ohne Container bleibt ein Text mit Insert-Tags unverändert, statt dass die
	 * Ausgabe verloren geht.
	 *
	 * @return void
	 */
	public function testOhneContainerBleibtDerTextUnveraendert(): void
	{
		$this->assertSame('{{env::url}}/contao', Scope::replaceInsertTags('{{env::url}}/contao'));
	}

	/**
	 * Ohne Container darf das Schreiben in die Sitzung nur melden, dass es nicht
	 * geklappt hat — abbrechen darf es nicht.
	 *
	 * @return void
	 */
	public function testOhneContainerKeinSchreibenInDieSitzung(): void
	{
		$this->assertFalse(Scope::setSessionValue('iccf_import', array('a' => 1)));
		$this->assertNull(Scope::getSessionValue('iccf_import'));
	}

	/**
	 * Das Protokollieren darf ohne Container ebenfalls nicht abbrechen.
	 *
	 * @return void
	 */
	public function testProtokollierenOhneContainerBrichtNichtAb(): void
	{
		Scope::log('Testeintrag', __METHOD__);

		$this->assertTrue(true);
	}
}
