<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Tests\Classes;

use Contao\StringUtil;
use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoFernschachBundle\Classes\Tokens;

/**
 * Prüft die Platzhalter-Ersetzung, die codefog/contao-haste abgelöst hat.
 */
class TokensTest extends TestCase
{
	/**
	 * Überspringt die Tests, wenn die Contao-Klassen nicht erreichbar sind.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		if (!class_exists(StringUtil::class))
		{
			$this->markTestSkipped('Ohne Contao-Autoloader (CONTAO_AUTOLOAD) sind diese Tests nicht ausführbar.');
		}
	}

	/**
	 * Ein leerer Vorlagentext ergibt eine leere Zeichenkette — auch wenn null
	 * übergeben wird, wie es bei einer nicht gefüllten Datenbankspalte vorkommt.
	 *
	 * @return void
	 */
	public function testLeererTextErgibtLeereZeichenkette(): void
	{
		$this->assertSame('', Tokens::replace(null, array()));
		$this->assertSame('', Tokens::replace('', array('a' => 'b')));
	}

	/**
	 * Ohne Container steht der Platzhalter-Parser nicht zur Verfügung. Der Text
	 * muss dann unverändert zurückkommen, statt verloren zu gehen.
	 *
	 * @return void
	 */
	public function testOhneContainerBleibtDerTextErhalten(): void
	{
		$this->assertSame('Hallo ##name##', Tokens::replace('Hallo ##name##', array('name' => 'Welt')));
	}

	/**
	 * HTML-Entitäten werden aufgelöst, damit die vom Editor maskierten Klammern
	 * der Insert-Tags wiedergefunden werden.
	 *
	 * @return void
	 */
	public function testEntitaetenWerdenAufgeloest(): void
	{
		$this->assertSame('{{env::url}}', Tokens::replace('&#123;&#123;env::url&#125;&#125;', array()));
	}

	/**
	 * Nicht-skalare Platzhalterwerte dürfen keinen Fehler auslösen, sondern
	 * werden zu leeren Zeichenketten.
	 *
	 * @return void
	 */
	public function testNichtSkalareWerteWerdenZuLeerenZeichenketten(): void
	{
		$ergebnis = Tokens::replace('X', array('feld' => array(1, 2), 'nichts' => null));

		$this->assertSame('X', $ergebnis);
	}
}
