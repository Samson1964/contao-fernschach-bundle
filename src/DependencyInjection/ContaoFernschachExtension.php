<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Lädt die Dienstbeschreibungen des Bundles in den Symfony-Container.
 */
class ContaoFernschachExtension extends Extension
{
	/**
	 * Liest src/Resources/config/services.yaml ein.
	 *
	 * @param array            $mergedConfig Die zusammengeführte Bundle-Konfiguration.
	 *                                       Das Bundle hat keine eigenen
	 *                                       Konfigurationsschlüssel, der Wert ist
	 *                                       deshalb immer leer
	 * @param ContainerBuilder $container    Der im Aufbau befindliche Container
	 *
	 * @return void
	 */
	public function load(array $mergedConfig, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader(
			$container,
			new FileLocator(__DIR__.'/../Resources/config')
		);

		$loader->load('services.yaml');
	}
}
