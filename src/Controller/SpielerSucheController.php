<?php

declare(strict_types=1);

/*
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoFernschachBundle\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FrontendUser;
use Schachbulle\ContaoFernschachBundle\Classes\Helper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Liefert Vorschläge für die Spielerauswahl im Mannschaftsmeldeformular.
 *
 * Das Formular hat die Spieler früher als vollständige Auswahlliste angeboten —
 * bei mehreren tausend Mitgliedern eine Liste, in der niemand etwas findet.
 * Stattdessen tippt man jetzt zwei Zeichen und bekommt die dazu passenden
 * Mitglieder vorgeschlagen.
 */
class SpielerSucheController
{
	/**
	 * Ab wie vielen Zeichen gesucht wird.
	 *
	 * Bei einem einzelnen Buchstaben käme praktisch die gesamte Mitgliederliste
	 * zurück; das hilft niemandem und belastet nur die Datenbank.
	 */
	private const MINDESTLAENGE = 2;

	/**
	 * Wie viele Vorschläge höchstens zurückgegeben werden.
	 */
	private const HOECHSTZAHL = 15;

	private ContaoFramework $framework;

	private TokenStorageInterface $tokenStorage;

	/**
	 * @param ContaoFramework       $framework    Startet das Contao-Framework, ohne das
	 *                                            es keine Datenbankverbindung gibt
	 * @param TokenStorageInterface $tokenStorage Liefert den angemeldeten Benutzer;
	 *                                            gesucht werden darf nur angemeldet
	 */
	public function __construct(ContaoFramework $framework, TokenStorageInterface $tokenStorage)
	{
		$this->framework = $framework;
		$this->tokenStorage = $tokenStorage;
	}

	/**
	 * Beantwortet eine Suchanfrage der Autovervollständigung.
	 *
	 * Gesucht wird in Nachname, Vorname, BdF-Mitgliedsnummer und ICCF-ID, sodass
	 * sich ein Spieler auch dann finden lässt, wenn man den Namen nicht sicher
	 * schreiben kann.
	 *
	 * @param Request $request Die laufende Anfrage; ausgewertet wird der Parameter
	 *                         "q" mit dem eingetippten Text
	 *
	 * @return JsonResponse Liste von Objekten mit den Schlüsseln "id" (Spieler-ID),
	 *                      "text" (Anzeigename) und "info" (Mitgliedsnummern).
	 *                      Leer bei zu kurzer Eingabe; Status 403 ohne Anmeldung
	 */
	public function __invoke(Request $request): JsonResponse
	{
		$this->framework->initialize();

		// Die Spielerliste ist kein öffentliches Verzeichnis. Ohne angemeldetes
		// Frontend-Mitglied gibt es deshalb keine Auskunft.
		$token = $this->tokenStorage->getToken();

		if (null === $token || !$token->getUser() instanceof FrontendUser)
		{
			return new JsonResponse(array('error' => 'Nicht angemeldet'), 403);
		}

		$suche = trim((string) $request->query->get('q', ''));

		if (mb_strlen($suche) < self::MINDESTLAENGE)
		{
			return new JsonResponse(array());
		}

		return new JsonResponse(Helper::sucheSpieler($suche, self::HOECHSTZAHL));
	}
}
