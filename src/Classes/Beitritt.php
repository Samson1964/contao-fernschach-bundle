<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Email;
use Contao\StringUtil;

/**
 * Die Beitrittserklärung zum BdF.
 *
 * Bis Version 2.12.0 war die Beitrittserklärung ein von Hand gebautes
 * Contao-Formular; das Bundle hat sich über den Hook processFormData nur die
 * abgeschickten Werte abgeholt (@see \Schachbulle\ContaoFernschachBundle\EventListener\Beitrittsformularpuefung).
 * Wer die Erweiterung neu installierte, musste das Formular mit seinen
 * neunzehn Feldern erst nachbauen, und ein umbenanntes Feld fiel niemandem auf
 * — der Datensatz entstand trotzdem, nur eben ohne diesen Wert.
 *
 * Hier steht deshalb alles beisammen: Welche Felder es gibt, was davon Pflicht
 * ist, wie geprüft wird, was in die Datenbank kommt und wer benachrichtigt
 * wird. Das Frontend-Modul @see \Schachbulle\ContaoFernschachBundle\Modules\Beitrittserklaerung
 * bringt nur noch die Ausgabe dazu.
 */
class Beitritt
{
	/**
	 * Beschreibt die Felder der Beitrittserklärung.
	 *
	 * Die Reihenfolge ist die Reihenfolge im Formular. Aus dieser einen Liste
	 * entstehen die Ausgabe, die Prüfung und der Text für die E-Mails — ein
	 * neues Feld ist damit an einer Stelle nachzutragen statt an vieren.
	 *
	 * Bedeutung der Schlüssel:
	 * - label:    Beschriftung im Formular und in den E-Mails
	 * - typ:      text, tel, email, textarea, radio, select, checkbox
	 * - pflicht:  true, wenn ohne diesen Wert nicht abgeschickt werden kann
	 * - spalte:   Zielspalte in tl_fernschach_spieler; fehlt sie, wandert der
	 *             Wert in den Fließtext des Feldes info_beitritt
	 * - hinweis:  Erklärung unter dem Feld
	 *
	 * @return array Feldname => Beschreibung
	 */
	public static function felder()
	{
		return array
		(
			'vorname'             => array('label' => 'Vorname', 'typ' => 'text', 'pflicht' => true, 'spalte' => 'vorname', 'maxlaenge' => 64),
			'nachname'            => array('label' => 'Nachname', 'typ' => 'text', 'pflicht' => true, 'spalte' => 'nachname', 'maxlaenge' => 64),
			'strasse'             => array('label' => 'Straße/Nr.', 'typ' => 'text', 'pflicht' => true, 'spalte' => 'strasse', 'maxlaenge' => 64),
			'plz'                 => array('label' => 'PLZ', 'typ' => 'text', 'pflicht' => true, 'spalte' => 'plz', 'maxlaenge' => 10),
			'ort'                 => array('label' => 'Wohnort', 'typ' => 'text', 'pflicht' => true, 'spalte' => 'ort', 'maxlaenge' => 64),
			'geburtstag'          => array('label' => 'Geburtsdatum', 'typ' => 'text', 'pflicht' => true, 'spalte' => 'birthday', 'hinweis' => 'TT.MM.JJJJ', 'maxlaenge' => 10),
			'staat'               => array('label' => 'Staatsangehörigkeit', 'typ' => 'text', 'pflicht' => true, 'maxlaenge' => 64),
			'telefon'             => array('label' => 'Telefon', 'typ' => 'tel', 'pflicht' => false, 'spalte' => 'telefon1', 'maxlaenge' => 64),
			'email'               => array('label' => 'E-Mail', 'typ' => 'email', 'pflicht' => false, 'spalte' => 'email1', 'hinweis' => 'Über diese Adresse läuft der Schriftwechsel mit dem BdF', 'maxlaenge' => 128),
			'bdf_mitglied'        => array('label' => 'Bereits BdF-Mitglied gewesen?', 'typ' => 'radio', 'pflicht' => true, 'optionen' => array('Ja' => 'Ja', 'Nein' => 'Nein')),
			'mitgliedsnummer'     => array('label' => 'Wenn ja, Mitglieds-Nr.', 'typ' => 'text', 'pflicht' => false, 'spalte' => 'memberId', 'maxlaenge' => 10),
			'fernschach_erfolge'  => array('label' => 'Bisherige Erfolge im Fernschach', 'typ' => 'textarea', 'pflicht' => false),
			'nahschach_erfolge'   => array('label' => 'Bisherige Erfolge im Nahschach', 'typ' => 'textarea', 'pflicht' => false),
			'elo'                 => array('label' => 'Nahschach-Elo', 'typ' => 'text', 'pflicht' => false, 'maxlaenge' => 4),
			'dwz'                 => array('label' => 'Nahschach-DWZ', 'typ' => 'text', 'pflicht' => false, 'maxlaenge' => 4),
			'beitrittsmonat'      => array('label' => 'Beitritt zum 1. des Monats', 'typ' => 'select', 'pflicht' => true),
			'beitrittszustimmung' => array('label' => 'Hiermit erkläre ich zum genannten Monat meinen Beitritt zum Deutschen Fernschachbund e. V. (BdF) und erkläre die Kenntnisnahme der Satzung.', 'typ' => 'checkbox', 'pflicht' => true),
			'datenschutz'         => array('label' => 'Ich willige ein, dass der Deutsche Fernschachbund die von mir übermittelten Daten verwendet, um mit mir in Kontakt zu treten und meinen Antrag abzuwickeln.', 'typ' => 'checkbox', 'pflicht' => true),
		);
	}

	/**
	 * Liefert die Monate, zu denen beigetreten werden kann.
	 *
	 * Angeboten werden der laufende Monat und die folgenden. Der Beitritt gilt
	 * immer zum Ersten, deshalb steht als Schlüssel JJJJMM.
	 *
	 * @param int      $intAnzahl   Wie viele Monate angeboten werden
	 * @param int|null $intZeitpunkt Stichtag als Zeitstempel; ohne Angabe gilt heute.
	 *                              Gebraucht wird das für die Prüfstände, sonst
	 *                              hinge der Test am Datum des Rechners
	 *
	 * @return array JJJJMM => „1. Oktober 2026"
	 */
	public static function monate($intAnzahl = 4, $intZeitpunkt = null)
	{
		$arrMonate = array();
		$intZeit = $intZeitpunkt ?: time();
		$intJahr = (int) date('Y', $intZeit);
		$intMonat = (int) date('n', $intZeit);

		$arrNamen = array(1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');

		for ($i = 0; $i < $intAnzahl; ++$i)
		{
			$intAktuell = $intMonat + $i;
			$intVersatz = (int) floor(($intAktuell - 1) / 12);
			$intSpalte = $intAktuell - 12 * $intVersatz;

			$arrMonate[sprintf('%04d%02d', $intJahr + $intVersatz, $intSpalte)] = '1. '.$arrNamen[$intSpalte].' '.($intJahr + $intVersatz);
		}

		return $arrMonate;
	}

	/**
	 * Prüft die eingegebenen Werte.
	 *
	 * Geprüft wird auf dem Server, nicht im Browser: Was ein Formular an
	 * Pflichtangaben verlangt, lässt sich mit den Werkzeugen jedes Browsers in
	 * zehn Sekunden abschalten.
	 *
	 * @param array    $arrWerte     Die eingegebenen Werte, Feldname => Wert
	 * @param int|null $intZeitpunkt Stichtag für die Monatsauswahl, siehe monate()
	 *
	 * @return array Feldname => Fehlertext. Leer, wenn alles in Ordnung ist
	 */
	public static function pruefe($arrWerte, $intZeitpunkt = null)
	{
		$arrFehler = array();

		foreach (self::felder() as $strName => $arrFeld)
		{
			$strWert = trim((string) ($arrWerte[$strName] ?? ''));

			if (!empty($arrFeld['pflicht']) && '' === $strWert)
			{
				$arrFehler[$strName] = 'checkbox' === $arrFeld['typ']
					? 'Ohne diese Zustimmung ist der Beitritt nicht möglich.'
					: 'Bitte ausfüllen.';

				continue;
			}

			if ('' === $strWert)
			{
				continue;
			}

			// Auswahlfelder: nur anbieten heißt nicht, dass auch nur das ankommt
			if (isset($arrFeld['optionen']) && !isset($arrFeld['optionen'][$strWert]))
			{
				$arrFehler[$strName] = 'Bitte wählen Sie eine der angebotenen Möglichkeiten.';

				continue;
			}

			if (isset($arrFeld['maxlaenge']) && mb_strlen($strWert) > $arrFeld['maxlaenge'])
			{
				$arrFehler[$strName] = 'Höchstens '.$arrFeld['maxlaenge'].' Zeichen.';
			}
		}

		// Geburtsdatum
		if (!isset($arrFehler['geburtstag']) && '' !== trim((string) ($arrWerte['geburtstag'] ?? '')))
		{
			$intGeburtstag = self::datum($arrWerte['geburtstag']);

			if (!$intGeburtstag)
			{
				$arrFehler['geburtstag'] = 'Bitte in der Form TT.MM.JJJJ angeben, zum Beispiel 24.03.1968.';
			}
			elseif ($intGeburtstag > (int) date('Ymd'))
			{
				$arrFehler['geburtstag'] = 'Das Geburtsdatum liegt in der Zukunft.';
			}
			elseif ($intGeburtstag < ((int) date('Y') - 120) * 10000)
			{
				$arrFehler['geburtstag'] = 'Bitte prüfen Sie das Geburtsdatum.';
			}
		}

		// E-Mail-Adresse
		$strEmail = trim((string) ($arrWerte['email'] ?? ''));

		if ('' !== $strEmail && !filter_var($strEmail, FILTER_VALIDATE_EMAIL))
		{
			$arrFehler['email'] = 'Diese E-Mail-Adresse sieht nicht richtig aus.';
		}

		// Wertungszahlen
		foreach (array('elo' => 'Elo-Zahl', 'dwz' => 'DWZ') as $strName => $strTitel)
		{
			$strWert = trim((string) ($arrWerte[$strName] ?? ''));

			if ('' === $strWert || isset($arrFehler[$strName]))
			{
				continue;
			}

			if (!ctype_digit($strWert) || (int) $strWert < 500 || (int) $strWert > 3500)
			{
				$arrFehler[$strName] = 'Bitte die '.$strTitel.' als Zahl zwischen 500 und 3500 angeben oder das Feld leer lassen.';
			}
		}

		// Beitrittsmonat: es zählt nur, was auch angeboten wurde
		$strMonat = trim((string) ($arrWerte['beitrittsmonat'] ?? ''));

		if ('' !== $strMonat && !isset($arrFehler['beitrittsmonat']) && !isset(self::monate(4, $intZeitpunkt)[$strMonat]))
		{
			$arrFehler['beitrittsmonat'] = 'Bitte wählen Sie einen der angebotenen Monate.';
		}

		return $arrFehler;
	}

	/**
	 * Wandelt ein eingegebenes Datum in die Speicherform JJJJMMTT.
	 *
	 * Angenommen werden TT.MM.JJJJ und, weil manche Browser das aus einem
	 * Datumsfeld schicken, JJJJ-MM-TT. Ein Datum, das es nicht gibt — der
	 * 31.02. etwa —, gilt als ungültig.
	 *
	 * @param string $strWert Die Eingabe
	 *
	 * @return int Das Datum als JJJJMMTT, oder 0 bei einer unbrauchbaren Eingabe
	 */
	public static function datum($strWert)
	{
		$strWert = trim((string) $strWert);

		if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $strWert, $arrTreffer))
		{
			list(, $intTag, $intMonat, $intJahr) = $arrTreffer;
		}
		elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $strWert, $arrTreffer))
		{
			list(, $intJahr, $intMonat, $intTag) = $arrTreffer;
		}
		else
		{
			return 0;
		}

		if (!checkdate((int) $intMonat, (int) $intTag, (int) $intJahr))
		{
			return 0;
		}

		return (int) sprintf('%04d%02d%02d', $intJahr, $intMonat, $intTag);
	}

	/**
	 * Baut den Fließtext für das Feld *Informationen zum Beitritt*.
	 *
	 * Hier landet alles, wofür es in tl_fernschach_spieler keine eigene Spalte
	 * gibt — die Staatsangehörigkeit etwa oder die bisherigen Erfolge. Der Text
	 * geht außerdem als Zusammenfassung an den Schatzmeister.
	 *
	 * @param array $arrWerte Die geprüften Werte
	 *
	 * @return string Der fertige Text, Zeile für Zeile
	 */
	public static function infotext($arrWerte)
	{
		$arrZeilen = array();
		$arrMonate = self::monate(12);

		foreach (self::felder() as $strName => $arrFeld)
		{
			// Was eine eigene Spalte hat, steht schon im Datensatz
			if (isset($arrFeld['spalte']))
			{
				continue;
			}

			$strWert = trim((string) ($arrWerte[$strName] ?? ''));

			if ('' === $strWert)
			{
				continue;
			}

			if ('beitrittsmonat' === $strName)
			{
				$strWert = $arrMonate[$strWert] ?? $strWert;
			}

			if ('checkbox' === $arrFeld['typ'])
			{
				$strWert = 'ja';
			}

			$arrZeilen[] = $arrFeld['label'].': '.$strWert;
		}

		$arrZeilen[] = 'Eingegangen am: '.date('d.m.Y H:i').' Uhr';

		return implode("\n", $arrZeilen);
	}

	/**
	 * Legt aus einer Beitrittserklärung den Spielerdatensatz an.
	 *
	 * Der Datensatz wird veröffentlicht angelegt — genauso, wie es der Hook auf
	 * das alte Contao-Formular gemacht hat. Er ist damit noch keine
	 * Mitgliedschaft: Die Mitgliedschaftszeiträume trägt die Geschäftsstelle
	 * ein, und ohne sie zählt der Datensatz in keiner Auswertung als Mitglied.
	 *
	 * @param array $arrWerte Die geprüften Werte
	 *
	 * @return int Die ID des neuen Datensatzes; 0, wenn das Anlegen scheiterte
	 */
	public static function speichere($arrWerte)
	{
		$arrSatz = array('tstamp' => time(), 'published' => '1');

		foreach (self::felder() as $strName => $arrFeld)
		{
			if (!isset($arrFeld['spalte']))
			{
				continue;
			}

			$strWert = trim((string) ($arrWerte[$strName] ?? ''));

			$arrSatz[$arrFeld['spalte']] = 'birthday' === $arrFeld['spalte'] ? self::datum($strWert) : $strWert;
		}

		// Eine Mitgliedsnummer gehört nur zu einer früheren Mitgliedschaft
		if ('Ja' !== ($arrWerte['bdf_mitglied'] ?? ''))
		{
			$arrSatz['memberId'] = '';
		}

		$arrSatz['info_beitritt'] = self::infotext($arrWerte);

		$objNeu = Database::getInstance()->prepare('INSERT INTO tl_fernschach_spieler %s')
		                                  ->set($arrSatz)
		                                  ->execute();

		$intId = (int) $objNeu->insertId;

		Scope::log(
			'[Fernschach-Verwaltung] Beitrittserklärung von '.$arrSatz['nachname'].', '.$arrSatz['vorname'].' übernommen (ID '.$intId.')',
			__METHOD__,
			ContaoContext::GENERAL
		);

		return $intId;
	}

	/**
	 * Sucht Datensätze, die zu derselben Person gehören könnten.
	 *
	 * Gesucht wird nach Name und Geburtsdatum. Ein Treffer hindert niemanden am
	 * Beitritt — wer schon einmal Mitglied war, tritt zu Recht ein zweites Mal
	 * ein. Er steht aber in der Nachricht an den Schatzmeister, damit dort
	 * nicht zwei Datensätze nebeneinander weiterlaufen.
	 *
	 * @param array $arrWerte Die geprüften Werte
	 *
	 * @return array Liste mit 'id', 'memberId' und 'name'
	 */
	public static function doppelgaenger($arrWerte)
	{
		$arrTreffer = array();

		$objSpieler = Database::getInstance()->prepare('SELECT id, memberId, nachname, vorname FROM tl_fernschach_spieler WHERE nachname = ? AND vorname = ? AND birthday = ?')
		                                      ->execute(
			                                      trim((string) ($arrWerte['nachname'] ?? '')),
			                                      trim((string) ($arrWerte['vorname'] ?? '')),
			                                      self::datum($arrWerte['geburtstag'] ?? '')
		                                      );

		while ($objSpieler->next())
		{
			$arrTreffer[] = array
			(
				'id'       => (int) $objSpieler->id,
				'memberId' => $objSpieler->memberId,
				'name'     => $objSpieler->nachname.', '.$objSpieler->vorname,
			);
		}

		return $arrTreffer;
	}

	/**
	 * Schickt die Beitrittserklärung an den Schatzmeister.
	 *
	 * Die Nachricht enthält alle eingegebenen Werte, einen Link auf den neuen
	 * Datensatz und den Hinweis auf mögliche Doppelgänger.
	 *
	 * @param int   $intId       ID des neuen Spielerdatensatzes
	 * @param array $arrWerte    Die geprüften Werte
	 * @param array $arrDoppelt  Ergebnis von doppelgaenger()
	 *
	 * @return bool True, wenn die Nachricht auf den Weg gebracht wurde. False,
	 *              wenn keine Adresse hinterlegt ist oder der Versand scheiterte;
	 *              in beiden Fällen steht der Grund im Systemprotokoll
	 */
	public static function meldeSchatzmeister($intId, $arrWerte, $arrDoppelt = array())
	{
		$strName = trim((string) ($arrWerte['nachname'] ?? '')).', '.trim((string) ($arrWerte['vorname'] ?? ''));
		$strEmpfaenger = trim((string) Config::get('fernschach_schatzmeisterEmail'));

		if ('' === $strEmpfaenger)
		{
			Scope::log(
				'[Fernschach-Verwaltung] Beitrittserklärung von '.$strName.' (ID '.$intId.'): Der Schatzmeister konnte nicht benachrichtigt werden, weil in den Einstellungen keine E-Mail-Adresse hinterlegt ist.',
				__METHOD__,
				ContaoContext::ERROR
			);

			return false;
		}

		$strSchatzmeister = trim((string) Config::get('fernschach_schatzmeisterName'));
		$strLink = Scope::replaceInsertTags('{{env::url}}').'/contao?do=fernschach-spieler&amp;act=edit&amp;id='.$intId;

		$strText = '<html><body>';
		$strText .= '<p>Hallo'.($strSchatzmeister ? ' '.StringUtil::specialchars($strSchatzmeister) : '').',</p>';
		$strText .= '<p>es ist eine Beitrittserklärung eingegangen:</p>';
		$strText .= '<ul>';

		foreach (self::felder() as $strFeld => $arrFeld)
		{
			$strWert = trim((string) ($arrWerte[$strFeld] ?? ''));

			if ('' === $strWert)
			{
				continue;
			}

			if ('beitrittsmonat' === $strFeld)
			{
				$strWert = self::monate(12)[$strWert] ?? $strWert;
			}

			if ('checkbox' === $arrFeld['typ'])
			{
				$strWert = 'ja';
			}

			$strText .= '<li>'.StringUtil::specialchars($arrFeld['label']).': <b>'.nl2br(StringUtil::specialchars($strWert)).'</b></li>';
		}

		$strText .= '</ul>';

		if ($arrDoppelt)
		{
			$strText .= '<p><b>Achtung:</b> Zu diesem Namen und Geburtsdatum gibt es bereits '.\count($arrDoppelt).' Datensatz/Datensätze:</p><ul>';

			foreach ($arrDoppelt as $arrEintrag)
			{
				$strText .= '<li>'.StringUtil::specialchars($arrEintrag['name']).' (ID '.$arrEintrag['id'].', BdF-Nr. '.StringUtil::specialchars((string) $arrEintrag['memberId']).')</li>';
			}

			$strText .= '</ul>';
		}

		$strText .= '<p><a href="'.$strLink.'">Neuen Datensatz im Backend öffnen</a></p>';
		$strText .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p>';
		$strText .= '</body></html>';

		try
		{
			$objEmail = new Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = Config::get('fernschach_emailAdresse');
			$objEmail->fromName = Config::get('fernschach_emailVon');
			$objEmail->subject = 'Beitrittserklärung '.$strName;
			$objEmail->html = $strText;

			if ('' !== trim((string) ($arrWerte['email'] ?? '')))
			{
				$objEmail->replyTo(trim((string) $arrWerte['email']));
			}

			$objEmail->sendTo(array($strSchatzmeister ? $strSchatzmeister.' <'.$strEmpfaenger.'>' : $strEmpfaenger));
		}
		catch (\Throwable $objFehler)
		{
			Scope::log(
				'[Fernschach-Verwaltung] Beitrittserklärung von '.$strName.' (ID '.$intId.'): Die Nachricht an den Schatzmeister ließ sich nicht verschicken: '.$objFehler->getMessage(),
				__METHOD__,
				ContaoContext::ERROR
			);

			return false;
		}

		return true;
	}

	/**
	 * Bestätigt dem Antragsteller den Eingang.
	 *
	 * Ohne E-Mail-Adresse — sie ist kein Pflichtfeld — unterbleibt die
	 * Bestätigung; der Antrag ist trotzdem gestellt.
	 *
	 * @param array $arrWerte Die geprüften Werte
	 *
	 * @return bool True, wenn die Bestätigung verschickt wurde
	 */
	public static function bestaetigeAntragsteller($arrWerte)
	{
		$strEmpfaenger = trim((string) ($arrWerte['email'] ?? ''));

		if ('' === $strEmpfaenger)
		{
			return false;
		}

		$strName = trim((string) ($arrWerte['vorname'] ?? '')).' '.trim((string) ($arrWerte['nachname'] ?? ''));
		$strMonat = self::monate(12)[$arrWerte['beitrittsmonat'] ?? ''] ?? '';

		$strText = '<html><body>';
		$strText .= '<p>Hallo '.StringUtil::specialchars($strName).',</p>';
		$strText .= '<p>Ihre Beitrittserklärung zum Deutschen Fernschachbund e. V. ist eingegangen'.($strMonat ? ' — zum '.StringUtil::specialchars($strMonat) : '').'.</p>';
		$strText .= '<p>Die Geschäftsstelle prüft Ihren Antrag und meldet sich bei Ihnen. Sollten Sie sich vertan haben oder etwas nachtragen wollen, antworten Sie einfach auf diese E-Mail.</p>';
		$strText .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p>';
		$strText .= '</body></html>';

		try
		{
			$objEmail = new Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = Config::get('fernschach_emailAdresse');
			$objEmail->fromName = Config::get('fernschach_emailVon');
			$objEmail->subject = 'Ihre Beitrittserklärung zum BdF';
			$objEmail->html = $strText;
			$objEmail->sendTo(array($strName.' <'.$strEmpfaenger.'>'));
		}
		catch (\Throwable $objFehler)
		{
			Scope::log(
				'[Fernschach-Verwaltung] Beitrittserklärung von '.$strName.': Die Bestätigung an den Antragsteller ließ sich nicht verschicken: '.$objFehler->getMessage(),
				__METHOD__,
				ContaoContext::ERROR
			);

			return false;
		}

		return true;
	}
}
