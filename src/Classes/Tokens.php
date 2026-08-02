<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\CoreBundle\String\SimpleTokenParser;
use Contao\StringUtil;
use Contao\System;

/**
 * Ersetzt Platzhalter (##token##) und Insert-Tags in E-Mail-Vorlagen.
 *
 * Bis Version 1.9.6 erledigte das \Haste\Util\StringUtil::recursiveReplaceTokensAndTags()
 * aus codefog/contao-haste. Haste 4 lässt sich unter Contao 5 nicht mehr
 * installieren, und Haste 5 enthält diese Methode nicht mehr. Diese Klasse bildet
 * ihr Verhalten mit Bordmitteln von Contao nach, die es in 4.13 wie in 5 gibt:
 * dem Dienst contao.string.simple_token_parser für die Platzhalter und
 * contao.insert_tag.parser für die Insert-Tags (siehe [[Scope]]).
 */
class Tokens
{
	/**
	 * Obergrenze für die Wiederholungen in replace().
	 *
	 * Ohne sie liefe die Ersetzung endlos, sobald ein Platzhalter oder ein
	 * Insert-Tag sich selbst wieder erzeugt — etwa ein Token, dessen Inhalt
	 * denselben Token enthält.
	 */
	private const MAX_DURCHLAEUFE = 10;

	/**
	 * Ersetzt in einem Text so lange Platzhalter und Insert-Tags, bis sich
	 * nichts mehr ändert.
	 *
	 * Die Wiederholung ist nötig, weil ein Platzhalter seinerseits Insert-Tags
	 * enthalten kann und ein Insert-Tag wiederum Platzhalter — genau das nutzt
	 * die Signatur aus den Benutzereinstellungen, in der ##benutzer_name## steht.
	 *
	 * Wie in Haste werden zuerst die HTML-Entitäten aufgelöst, weil der
	 * TinyMCE-Editor die Klammern der Insert-Tags gern maskiert und die
	 * Platzhalter sonst nicht gefunden würden.
	 *
	 * @param string|null $strText   Der Vorlagentext; null wird als leerer Text behandelt
	 * @param array       $arrTokens Platzhaltername (ohne ##) => Ersatzwert. Werte
	 *                               dürfen null sein und werden dann zu ''
	 *
	 * @return string Der fertige Text. Bei einem Fehler im Platzhalter-Parser
	 *                (etwa einer kaputten {if}-Bedingung in der Vorlage) wird der
	 *                bis dahin erreichte Stand zurückgegeben, damit ein Tippfehler
	 *                in einer Vorlage nicht die ganze Backend-Seite lahmlegt
	 */
	public static function replace(?string $strText, array $arrTokens): string
	{
		if (null === $strText || '' === $strText)
		{
			return '';
		}

		// Der Platzhalter-Parser erwartet Zeichenketten. Aus der Datenbank
		// kommen für leere Spalten aber null-Werte, die unter PHP 8.1 eine
		// Deprecation-Warnung auslösen würden.
		foreach ($arrTokens as $strName => $varValue)
		{
			$arrTokens[$strName] = is_scalar($varValue) ? (string) $varValue : '';
		}

		$strText = StringUtil::decodeEntities($strText);

		for ($i = 0; $i < self::MAX_DURCHLAEUFE; ++$i)
		{
			$strVorher = $strText;
			$strText = self::parseSimpleTokens($strText, $arrTokens);
			$strText = Scope::replaceInsertTags($strText);

			if ($strText === $strVorher)
			{
				break;
			}
		}

		return $strText;
	}

	/**
	 * Ersetzt ausschließlich die ##...##-Platzhalter in einem Text.
	 *
	 * Ausgelagert, damit replace() lesbar bleibt und die Ausnahmebehandlung an
	 * einer Stelle steht.
	 *
	 * @param string $strText   Der zu bearbeitende Text
	 * @param array  $arrTokens Platzhaltername => Ersatzwert, alle Werte Zeichenketten
	 *
	 * @return string Der Text mit ersetzten Platzhaltern, oder unverändert, wenn
	 *                der Parser die Vorlage nicht verarbeiten konnte
	 */
	private static function parseSimpleTokens(string $strText, array $arrTokens): string
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('contao.string.simple_token_parser'))
		{
			return $strText;
		}

		$objParser = $container->get('contao.string.simple_token_parser');

		if (!$objParser instanceof SimpleTokenParser)
		{
			return $strText;
		}

		try
		{
			return $objParser->parse($strText, $arrTokens);
		}
		catch (\Exception $e)
		{
			// Der Parser wirft unter anderem bei fehlerhaften {if}-Bedingungen.
			// Der Rohtext ist in dem Fall die bessere Ausgabe als eine leere
			// Seite oder ein Fehlerbildschirm im Backend.
			return $strText;
		}
	}
}
