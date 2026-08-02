<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Tests;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoFernschachBundle\ContaoFernschachBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Prüft die Haupt-Bundle-Klasse.
 */
class ContaoFernschachBundleTest extends TestCase
{
	/**
	 * Das Bundle muss ein Symfony-Bundle sein, sonst lädt es der Kernel nicht.
	 *
	 * @return void
	 */
	public function testIstEinSymfonyBundle(): void
	{
		$this->assertInstanceOf(Bundle::class, new ContaoFernschachBundle());
	}

	/**
	 * isContao5() muss eine Aussage treffen, ohne die in Contao 5 entfallene
	 * Konstante VERSION zu benutzen.
	 *
	 * Geprüft wird gegen den Composer-Laufzeitindex: Ist dort eine Version von
	 * contao/core-bundle hinterlegt, muss das Ergebnis dazu passen.
	 *
	 * @return void
	 */
	public function testErkenntDieContaoVersion(): void
	{
		if (!class_exists(InstalledVersions::class) || !InstalledVersions::isInstalled('contao/core-bundle'))
		{
			$this->markTestSkipped('Ohne installiertes contao/core-bundle lässt sich die Version nicht prüfen.');
		}

		$erwartet = version_compare((string) InstalledVersions::getVersion('contao/core-bundle'), '5.0.0', '>=');

		$this->assertSame($erwartet, ContaoFernschachBundle::isContao5());
	}

	/**
	 * Ohne Composer-Laufzeitindex darf die Methode nicht mit einem Fehler
	 * abbrechen, sondern muss "kein Contao 5" melden.
	 *
	 * @return void
	 */
	public function testLiefertImmerEinenWahrheitswert(): void
	{
		$this->assertIsBool(ContaoFernschachBundle::isContao5());
	}
}
