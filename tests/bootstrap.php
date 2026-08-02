<?php

declare(strict_types=1);

/**
 * Startdatei für die Tests dieses Bundles.
 *
 * Das Bundle hat kein eigenes vendor/-Verzeichnis (PHPUnit liegt zentral unter
 * F:\Claude\tools\phpunit9), deshalb wird der Autoloader hier von Hand
 * eingerichtet.
 *
 * Einige Tests brauchen die Contao-Klassen. Gefunden werden sie entweder über
 * ein vorhandenes vendor/-Verzeichnis des Bundles oder über die Umgebungs-
 * variable CONTAO_AUTOLOAD, die auf die vendor/autoload.php einer
 * Contao-Installation zeigt:
 *
 *   CONTAO_AUTOLOAD=F:/Claude/contao-test/vendor/autoload.php \
 *   F:/Claude/tools/phpunit9/vendor/bin/phpunit
 *
 * Fehlt beides, überspringen die betroffenen Tests sich selbst, statt mit einem
 * Fehler abzubrechen.
 */

$eigenerAutoloader = __DIR__.'/../vendor/autoload.php';
$fremderAutoloader = getenv('CONTAO_AUTOLOAD');

if (file_exists($eigenerAutoloader))
{
	require_once $eigenerAutoloader;
}
elseif ($fremderAutoloader && file_exists($fremderAutoloader))
{
	require_once $fremderAutoloader;
}

spl_autoload_register(
	static function (string $class): void
	{
		$praefixe = array(
			'Schachbulle\\ContaoFernschachBundle\\Tests\\' => __DIR__.'/',
			'Schachbulle\\ContaoFernschachBundle\\'        => __DIR__.'/../src/',
		);

		foreach ($praefixe as $praefix => $verzeichnis)
		{
			if (0 !== strpos($class, $praefix))
			{
				continue;
			}

			$datei = $verzeichnis.str_replace('\\', '/', substr($class, \strlen($praefix))).'.php';

			if (file_exists($datei))
			{
				require_once $datei;
			}

			return;
		}
	}
);

// Feste Zeitzone, damit Datumsangaben unabhängig von der Einstellung des
// jeweiligen Rechners immer dasselbe Ergebnis liefern.
date_default_timezone_set('Europe/Berlin');
