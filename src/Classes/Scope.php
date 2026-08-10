<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Contao\Versions;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

/**
 * Beantwortet die Frage, ob gerade das Backend oder das Frontend läuft.
 *
 * Bis Contao 4.13 stand dafür die Konstante TL_MODE zur Verfügung. Sie ist in
 * Contao 5 ersatzlos entfallen; die Auskunft gibt seitdem der Dienst
 * contao.routing.scope_matcher, den es in Contao 4.13 aber ebenfalls schon gibt.
 * Diese Klasse kapselt den Aufruf, damit die Modulklassen nicht jedes Mal drei
 * Zeilen Containerkram enthalten müssen.
 */
class Scope
{
	/**
	 * Prüft, ob die laufende Anfrage im Backend verarbeitet wird.
	 *
	 * Gebraucht wird das in den Frontend-Modulen: In der Modulübersicht des
	 * Backends soll statt der eigentlichen Ausgabe nur ein Platzhalter
	 * erscheinen.
	 *
	 * @return bool True im Backend. False im Frontend und ebenso dann, wenn es
	 *              gar keine Anfrage gibt — etwa im Cronjob oder auf der
	 *              Kommandozeile, wo eine Backend-Ausgabe ohnehin sinnlos wäre
	 */
	public static function isBackendRequest(): bool
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('contao.routing.scope_matcher'))
		{
			return false;
		}

		$request = $container->get('request_stack')->getCurrentRequest();

		if (null === $request)
		{
			return false;
		}

		return $container->get('contao.routing.scope_matcher')->isBackendRequest($request);
	}

	/**
	 * Liefert den aktuellen CSRF-Token für Formulare und Backend-Links.
	 *
	 * Ersetzt die Konstante REQUEST_TOKEN, die es in Contao 5 nicht mehr gibt.
	 * Der zuständige Dienst heißt in beiden Versionen contao.csrf.token_manager;
	 * getDefaultTokenValue() gibt es seit Contao 4.13.
	 *
	 * @return string Der Token, oder eine leere Zeichenkette, wenn der Dienst
	 *                nicht zur Verfügung steht (etwa in einem Unit-Test ohne
	 *                Container)
	 */
	public static function getRequestToken(): string
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('contao.csrf.token_manager'))
		{
			return '';
		}

		return (string) $container->get('contao.csrf.token_manager')->getDefaultTokenValue();
	}

	/**
	 * Liefert den Inhalt der Backend-Sitzung als Feld.
	 *
	 * Ersetzt Session::getInstance()->getData(). Die Klasse Contao\Session ist in
	 * Contao 5 entfallen; ihre Daten liegen seit Contao 4.9 im Symfony-Sitzungs-
	 * beutel "contao_backend", den es in beiden Versionen gibt. Gebraucht wird
	 * das für die gemerkten Sortierungen und Filter der Listenansichten.
	 *
	 * @return array Der Inhalt des Beutels, oder ein leeres Feld, wenn es keine
	 *               Sitzung gibt — etwa im Cronjob oder auf der Kommandozeile
	 */
	public static function getBackendSession(): array
	{
		$bag = self::getBackendSessionBag();

		return null === $bag ? array() : $bag->all();
	}

	/**
	 * Schreibt einen Eintrag ins Contao-Systemprotokoll.
	 *
	 * Ersetzt System::log() und Controller::log(), die in Contao 5 entfallen
	 * sind. Geschrieben wird über denselben Monolog-Kanal, den auch der
	 * Contao-Kern benutzt, damit der Eintrag im Backend unter "System-Log"
	 * auftaucht und dort nach Kategorie filterbar bleibt.
	 *
	 * @param string $strText     Der Protokolltext
	 * @param string $strFunction Aufrufende Methode, üblicherweise __METHOD__.
	 *                            Steht im Backend in der Spalte "Funktion"
	 * @param string $strCategory Kategorie aus ContaoContext, etwa
	 *                            ContaoContext::GENERAL oder ContaoContext::ERROR.
	 *                            Unbekannte Kategorien landen im allgemeinen Kanal
	 *
	 * @return void Ohne Container (etwa im Unit-Test) passiert nichts; ein
	 *              fehlgeschlagener Protokolleintrag darf den Aufrufer nicht
	 *              aus dem Tritt bringen
	 */
	public static function log(string $strText, string $strFunction, string $strCategory = ContaoContext::GENERAL): void
	{
		$container = System::getContainer();

		if (null === $container)
		{
			return;
		}

		$dienst = 'monolog.logger.contao.'.strtolower($strCategory);

		if (!$container->has($dienst))
		{
			$dienst = 'monolog.logger.contao';
		}

		if (!$container->has($dienst))
		{
			return;
		}

		$stufe = ContaoContext::ERROR === $strCategory ? LogLevel::ERROR : LogLevel::INFO;

		$container->get($dienst)->log($stufe, $strText, array('contao' => new ContaoContext($strFunction, $strCategory)));
	}

	/**
	 * Schreibt eine Zeile in eine eigene Protokolldatei unter var/logs.
	 *
	 * Ersatz für die globale Funktion log_message(), die es in Contao 5 nicht
	 * mehr gibt. Benutzt wird sie von den CSV-Importen, die jede gelesene Zeile
	 * mitschreiben — dafür wäre das Systemprotokoll der falsche Ort, weil es
	 * dort pro Import Zehntausende Einträge gäbe.
	 *
	 * Format und Ablageort entsprechen dem, was log_message() getan hat, damit
	 * bestehende Auswertungen weiter funktionieren.
	 *
	 * @param string $strText  Die zu protokollierende Zeile
	 * @param string $strDatei Dateiname innerhalb von var/logs, etwa
	 *                         'fernschach-verwaltung.log'
	 *
	 * @return void Lässt sich das Verzeichnis nicht ermitteln (etwa im Unit-Test
	 *              ohne Container), wird nichts geschrieben
	 */
	public static function logToFile(string $strText, string $strDatei): void
	{
		$container = System::getContainer();

		if (null === $container)
		{
			return;
		}

		$verzeichnis = $container->hasParameter('kernel.logs_dir') ? $container->getParameter('kernel.logs_dir') : null;

		if (!$verzeichnis && $container->hasParameter('kernel.project_dir'))
		{
			$verzeichnis = $container->getParameter('kernel.project_dir').'/var/logs';
		}

		if (!$verzeichnis || !is_dir($verzeichnis))
		{
			return;
		}

		error_log(sprintf("[%s] %s\n", date('d-M-Y H:i:s'), $strText), 3, $verzeichnis.'/'.$strDatei);
	}

	/**
	 * Legt eine neue Version eines Datensatzes an.
	 *
	 * Ersetzt Controller::createNewVersion(), das in Contao 5 entfallen ist. Der
	 * Rumpf entspricht dem, was die alte Methode getan hat, damit sich am
	 * Verhalten nichts ändert.
	 *
	 * @param string     $strTable Name der Tabelle, etwa 'tl_fernschach_spieler'
	 * @param int|string $intId    ID des Datensatzes
	 *
	 * @return void Der Versionsverwalter legt nur dann eine Version an, wenn die
	 *              Tabelle im DCA überhaupt Versionen führt
	 */
	public static function createVersion(string $strTable, $intId): void
	{
		$objVersions = new Versions($strTable, (int) $intId);
		$objVersions->create();
	}

	/**
	 * Legt die Ausgangsversion eines Datensatzes an.
	 *
	 * Ersetzt Controller::createInitialVersion(). Aufzurufen, bevor ein
	 * Datensatz geändert wird — sonst hat der Versionsvergleich im Backend
	 * keinen Stand, mit dem er vergleichen könnte.
	 *
	 * @param string     $strTable Name der Tabelle
	 * @param int|string $intId    ID des Datensatzes
	 *
	 * @return void
	 */
	public static function initializeVersion(string $strTable, $intId): void
	{
		$objVersions = new Versions($strTable, (int) $intId);
		$objVersions->initialize();
	}

	/**
	 * Liest einen einzelnen Wert aus dem Backend-Sitzungsbeutel.
	 *
	 * Ersatz für $this->Session->get() beziehungsweise $dc->Session->get(). Das
	 * Objekt hinter der Eigenschaft Session war eine Instanz der in Contao 5
	 * entfallenen Klasse Contao\Session; in Contao 5 liefert der Zugriff
	 * kommentarlos null, was beim nächsten Methodenaufruf zum Absturz führt.
	 *
	 * @param string $strKey      Schlüssel im Sitzungsbeutel
	 * @param mixed  $varStandard Rückgabewert, wenn der Schlüssel fehlt
	 *
	 * @return mixed Der gespeicherte Wert, sonst $varStandard
	 */
	public static function getBackendSessionValue(string $strKey, $varStandard = null)
	{
		$bag = self::getBackendSessionBag();

		if (null === $bag)
		{
			return $varStandard;
		}

		return $bag->has($strKey) ? $bag->get($strKey) : $varStandard;
	}

	/**
	 * Schreibt einen einzelnen Wert in den Backend-Sitzungsbeutel.
	 *
	 * @param string $strKey   Schlüssel im Sitzungsbeutel
	 * @param mixed  $varValue Der zu merkende Wert
	 *
	 * @return void Ohne Sitzung geht der Wert verloren; das ist hinnehmbar, weil
	 *              alle Aufrufer nur Bedienzustände wie Filter und aufgeklappte
	 *              Knoten ablegen
	 */
	public static function setBackendSessionValue(string $strKey, $varValue): void
	{
		$bag = self::getBackendSessionBag();

		if (null !== $bag)
		{
			$bag->set($strKey, $varValue);
		}
	}

	/**
	 * Ersetzt den gesamten Inhalt des Backend-Sitzungsbeutels.
	 *
	 * Ersatz für $this->Session->setData(). Wird gebraucht, wenn ein ganzer,
	 * zuvor über getBackendSession() gelesener Datenbestand zurückgeschrieben
	 * wird — etwa nachdem ein Filter aus der Liste entfernt wurde.
	 *
	 * @param array $arrData Der neue Inhalt des Beutels
	 *
	 * @return void
	 */
	public static function setBackendSession(array $arrData): void
	{
		$bag = self::getBackendSessionBag();

		if (null !== $bag)
		{
			$bag->replace($arrData);
		}
	}

	/**
	 * Liefert den Sitzungsbeutel des Backends.
	 *
	 * Gemeinsamer Unterbau der drei Methoden darüber und von
	 * getBackendSession(). Der Beutel heißt in Contao 4.13 wie in Contao 5
	 * "contao_backend".
	 *
	 * Zurückgegeben wird nur ein AttributeBagInterface: Die allgemeine
	 * Schnittstelle SessionBagInterface kennt weder get() noch set(). In der
	 * Praxis liefert Contao dort einen ArrayAttributeBag, die Prüfung schützt
	 * aber davor, dass eine abweichende Konfiguration in einen Fehler läuft.
	 *
	 * @return AttributeBagInterface|null Der Beutel, oder null wenn es keine
	 *                                    gestartete Sitzung gibt
	 */
	private static function getBackendSessionBag(): ?AttributeBagInterface
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('request_stack'))
		{
			return null;
		}

		$request = $container->get('request_stack')->getCurrentRequest();

		if (null === $request || !$request->hasSession())
		{
			return null;
		}

		$session = $request->getSession();

		// Ohne gestartete Sitzung würde das Lesen sie unnötig anlegen — und im
		// Cronjob oder auf der Kommandozeile gibt es gar keine.
		if (!$session->isStarted())
		{
			return null;
		}

		$bag = $session->getBag('contao_backend');

		return $bag instanceof AttributeBagInterface ? $bag : null;
	}

	/**
	 * Liest einen Wert aus der Sitzung des angemeldeten Benutzers.
	 *
	 * Gebraucht wird das vom mehrstufigen ICCF-Import, der sich zwischen zwei
	 * Aufrufen merken muss, welche Datei er gerade verarbeitet. Der frühere
	 * Container-Dienst "session" existiert seit Symfony 6 nicht mehr; der Weg
	 * über die Anfrage funktioniert in Contao 4.13 wie in Contao 5.
	 *
	 * @param string $strKey       Schlüssel in der Sitzung
	 * @param mixed  $varStandard  Rückgabewert, wenn der Schlüssel fehlt
	 *
	 * @return mixed Der gespeicherte Wert, sonst $varStandard
	 */
	public static function getSessionValue(string $strKey, $varStandard = null)
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('request_stack'))
		{
			return $varStandard;
		}

		$request = $container->get('request_stack')->getCurrentRequest();

		if (null === $request || !$request->hasSession())
		{
			return $varStandard;
		}

		return $request->getSession()->get($strKey, $varStandard);
	}

	/**
	 * Legt einen Wert in der Sitzung des angemeldeten Benutzers ab.
	 *
	 * @param string $strKey   Schlüssel in der Sitzung
	 * @param mixed  $varValue Der zu merkende Wert
	 *
	 * @return bool True, wenn der Wert abgelegt wurde; false, wenn es keine
	 *              Sitzung gibt und der Wert damit verloren geht
	 */
	public static function setSessionValue(string $strKey, $varValue): bool
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('request_stack'))
		{
			return false;
		}

		$request = $container->get('request_stack')->getCurrentRequest();

		if (null === $request || !$request->hasSession())
		{
			return false;
		}

		$request->getSession()->set($strKey, $varValue);

		return true;
	}

	/**
	 * Löst die Insert-Tags eines Textes auf.
	 *
	 * Ersatz für Controller::replaceInsertTags(), das in Contao 5 entfallen ist.
	 * Der Dienst contao.insert_tag.parser existiert in Contao 4.13 und in
	 * Contao 5 gleichermaßen.
	 *
	 * @param string $text Der Text, der Insert-Tags enthalten darf
	 *
	 * @return string Der Text mit aufgelösten Insert-Tags; unverändert, wenn der
	 *                Dienst nicht zur Verfügung steht
	 */
	public static function replaceInsertTags(string $text): string
	{
		$container = System::getContainer();

		if (null === $container || !$container->has('contao.insert_tag.parser'))
		{
			return $text;
		}

		return $container->get('contao.insert_tag.parser')->replace($text);
	}
}
