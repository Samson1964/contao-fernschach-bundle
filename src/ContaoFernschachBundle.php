<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle;

use Composer\InstalledVersions;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Haupt-Bundle-Klasse der Fernschach-Verwaltung.
 */
class ContaoFernschachBundle extends Bundle
{
	/**
	 * Prüft anhand der installierten Version, ob Contao 5 (oder neuer) läuft.
	 *
	 * Gebraucht wird das an den wenigen Stellen, an denen sich 4.13 und 5
	 * wirklich unterschiedlich verhalten — etwa beim Zugriff auf die
	 * Backend-Sitzung, die in Contao 5 nicht mehr über die entfallene Klasse
	 * Contao\Session erreichbar ist.
	 *
	 * Ausgewertet wird der Laufzeitindex von Composer und nicht die Konstante
	 * VERSION, da diese in Contao 5 entfallen ist.
	 *
	 * @return bool True bei Contao 5 oder neuer, false bei Contao 4.13 und
	 *              ebenso dann, wenn sich die Version nicht ermitteln lässt
	 */
	public static function isContao5(): bool
	{
		if (!class_exists(InstalledVersions::class))
		{
			return false;
		}

		return version_compare(
			(string) InstalledVersions::getVersion('contao/core-bundle'),
			'5.0.0',
			'>='
		);
	}
}
