<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Schachbulle\ContaoFernschachBundle\ContaoFernschachBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Meldet das Bundle beim Contao Manager an.
 */
class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
	/**
	 * Gibt die Bundle-Konfiguration zurück.
	 *
	 * Das Bundle wird nach dem Contao-Kern geladen, damit dessen DCA-Dateien
	 * und Dienste bereits vorhanden sind, wenn die eigenen Ergänzungen greifen.
	 *
	 * @param ParserInterface $parser Wird vom Contao Manager übergeben und hier
	 *                                nicht gebraucht, weil keine externe
	 *                                Konfigurationsdatei eingelesen wird
	 *
	 * @return array<BundleConfig> Liste mit der einen Bundle-Konfiguration
	 */
	public function getBundles(ParserInterface $parser)
	{
		return array
		(
			BundleConfig::create(ContaoFernschachBundle::class)
				->setLoadAfter(array(ContaoCoreBundle::class)),
		);
	}

	/**
	 * Bindet die Routen des Bundles in die Anwendung ein.
	 *
	 * Ohne diese Methode würde src/Resources/config/routes.yaml nicht geladen,
	 * und der ICCF-Import hätte keinen erreichbaren Endpunkt. Die Datei
	 * beschreibt die Routen bewusst ausdrücklich statt über Attribute, weil der
	 * Attribut-Lader in Symfony 5.4 (Contao 4.13) und Symfony 7 (Contao 5)
	 * unterschiedlich heißt.
	 *
	 * @param LoaderResolverInterface $resolver Liefert den passenden Lader für
	 *                                          die YAML-Routingdatei
	 * @param KernelInterface         $kernel   Der laufende Kernel, hier ungenutzt
	 *
	 * @return RouteCollection|null Die Routen des Bundles, oder null wenn kein
	 *                              Lader für YAML-Dateien zur Verfügung steht
	 */
	public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
	{
		$file = __DIR__.'/../Resources/config/routes.yaml';

		$loader = $resolver->resolve($file);

		if (false === $loader)
		{
			return null;
		}

		return $loader->load($file);
	}
}
