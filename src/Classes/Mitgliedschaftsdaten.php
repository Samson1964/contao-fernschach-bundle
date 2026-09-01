<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Environment;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\Versions;

/**
 * Bereinigt Mitgliedschaften mit Datumsangaben in der Anzeigeform.
 *
 * Gespeichert wird JJJJMMTT. Im Bestand stehen aber auch Datensätze in der Form
 * TT.MM.JJJJ; woher sie stammen, ließ sich nicht mehr klären. Auffallen können
 * sie nicht: Die Anzeige reicht punktierte Werte unverändert durch, und ein
 * Speichern im Backend wandelt sie stillschweigend um.
 *
 * Seit Version 2.9.2 werden sie beim Lesen richtig ausgewertet, sodass daraus
 * kein falsches Verhalten mehr entsteht. Diese Routine räumt sie zusätzlich in
 * der Datenbank auf — erst als Bericht, dann auf Rückfrage.
 */
class Mitgliedschaftsdaten extends Backend
{
	/**
	 * Wert des Parameters `key`, unter dem die Routine erreichbar ist.
	 */
	private const AKTION = 'pruefeMitgliedschaften';

	/**
	 * Namen der Spieler, die übersprungen wurden, weil sich keine Version anlegen
	 * ließ. Sie werden dem Benutzer nach dem Lauf genannt.
	 *
	 * @var array
	 */
	protected $arrUebersprungen = array();

	/**
	 * Erlaubt das Erzeugen der Klasse durch Contao.
	 *
	 * Contao ruft die hinterlegte Routine über import() auf, und das erzeugt die
	 * Klasse mit `new`. In Contao 4.13 ist der Konstruktor von Backend aber
	 * geschützt — ohne diesen eigenen Konstruktor bricht der Aufruf dort mit
	 * „Call to protected Contao\Backend::__construct()" ab. Alle übrigen
	 * Routinen des Bundles halten es genauso.
	 */
	public function __construct()
	{
	}

	/**
	 * Zeigt den Bericht an und bereinigt auf Rückfrage.
	 *
	 * Ohne den Parameter `bereinigen` wird nur gesucht und aufgelistet; nichts
	 * wird verändert. Mit `bereinigen=1` werden die gefundenen Datensätze
	 * umgeschrieben. Vor jeder Änderung entsteht eine Version, sodass sich der
	 * alte Stand zurückholen lässt, und jede Änderung steht im Systemprotokoll.
	 *
	 * @return string Die Ausgabe für das Backend; leer, wenn die Routine gar
	 *                nicht angesprochen wurde
	 */
	public function run()
	{
		if (Input::get('key') != self::AKTION)
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		$this->import(BackendUser::class, 'User');

		$arrBetroffen = self::suchen();

		if (Input::get('bereinigen'))
		{
			$intGeaendert = $this->bereinigen($arrBetroffen);

			Message::addConfirmation(1 === $intGeaendert
				? 'Ein Spielerdatensatz wurde bereinigt.'
				: $intGeaendert.' Spielerdatensätze wurden bereinigt.');

			if ($this->arrUebersprungen)
			{
				Message::addError(\count($this->arrUebersprungen).' Datensätze wurden übersprungen, weil sich keine Version anlegen ließ: '.implode(', ', $this->arrUebersprungen));
			}

			Controller::redirect(self::rueckweg());
		}

		return self::bericht($arrBetroffen);
	}

	/**
	 * Sucht alle Spieler mit mindestens einer punktierten Datumsangabe.
	 *
	 * Geprüft werden alle Datensätze, auch archivierte und nicht veröffentlichte:
	 * Auswertungen greifen ebenfalls auf sie zu.
	 *
	 * @return array Liste mit 'id', 'name', 'memberId' und den betroffenen
	 *               Mitgliedschaften als 'alt' und 'neu'
	 */
	protected function suchen()
	{
		$arrBetroffen = array();

		$objSpieler = Database::getInstance()->execute('SELECT id, nachname, vorname, memberId, memberships FROM tl_fernschach_spieler ORDER BY nachname ASC, vorname ASC');

		while ($objSpieler->next())
		{
			$arrMitgliedschaften = StringUtil::deserialize($objSpieler->memberships);

			if (!\is_array($arrMitgliedschaften))
			{
				continue;
			}

			$arrZeilen = array();

			foreach ($arrMitgliedschaften as $arrMitgliedschaft)
			{
				$strVon = (string) ($arrMitgliedschaft['from'] ?? '');
				$strBis = (string) ($arrMitgliedschaft['to'] ?? '');

				// Betroffen ist, was nicht rein aus Ziffern besteht. Der leere Wert
				// und die 0 sind in Ordnung.
				$blnVon = '' !== $strVon && !ctype_digit($strVon);
				$blnBis = '' !== $strBis && !ctype_digit($strBis);

				if (!$blnVon && !$blnBis)
				{
					continue;
				}

				$arrZeilen[] = array
				(
					'alt' => ($strVon !== '' ? $strVon : '0').' bis '.($strBis !== '' ? $strBis : '0'),
					'neu' => Helper::mitgliedschaftsdatum($strVon).' bis '.Helper::mitgliedschaftsdatum($strBis),
				);
			}

			if ($arrZeilen)
			{
				$arrBetroffen[] = array
				(
					'id'       => (int) $objSpieler->id,
					'name'     => $objSpieler->nachname.', '.$objSpieler->vorname,
					'memberId' => $objSpieler->memberId,
					'zeilen'   => $arrZeilen,
				);
			}
		}

		return $arrBetroffen;
	}

	/**
	 * Schreibt die gefundenen Datensätze in der Speicherform zurück.
	 *
	 * Verändert werden ausschließlich die Felder 'from' und 'to'; alles andere an
	 * der Mitgliedschaft — insbesondere der Status — bleibt unangetastet.
	 *
	 * @param array $arrBetroffen Ergebnis aus suchen()
	 *
	 * @return int Zahl der geänderten Spielerdatensätze
	 */
	protected function bereinigen($arrBetroffen)
	{
		$intGeaendert = 0;

		foreach ($arrBetroffen as $arrSpieler)
		{
			$objSpieler = Database::getInstance()->prepare('SELECT memberships FROM tl_fernschach_spieler WHERE id = ?')
			                                      ->execute($arrSpieler['id']);

			if (!$objSpieler->numRows)
			{
				continue;
			}

			$arrMitgliedschaften = StringUtil::deserialize($objSpieler->memberships);

			if (!\is_array($arrMitgliedschaften))
			{
				continue;
			}

			foreach ($arrMitgliedschaften as $intIndex => $arrMitgliedschaft)
			{
				$arrMitgliedschaften[$intIndex]['from'] = (string) Helper::mitgliedschaftsdatum($arrMitgliedschaft['from'] ?? 0);
				$arrMitgliedschaften[$intIndex]['to'] = (string) Helper::mitgliedschaftsdatum($arrMitgliedschaft['to'] ?? 0);
			}

			// Version vor der Änderung anlegen, damit sich der alte Stand über die
			// Versionsverwaltung zurückholen lässt. Gelingt das nicht, bleibt der
			// Datensatz unangetastet: Ohne Rückweg wird hier nichts geändert.
			// Ein Abbruch der ganzen Routine wäre schlechter — dann wäre der
			// Bestand halb bereinigt und niemand wüsste, wie weit.
			try
			{
				$objVersions = new Versions('tl_fernschach_spieler', $arrSpieler['id']);
				$objVersions->initialize();
			}
			catch (\Throwable $objFehler)
			{
				$this->arrUebersprungen[] = $arrSpieler['name'].' ('.$objFehler->getMessage().')';

				continue;
			}

			Database::getInstance()->prepare('UPDATE tl_fernschach_spieler %s WHERE id = ?')
			                        ->set(array('tstamp' => time(), 'memberships' => serialize($arrMitgliedschaften)))
			                        ->execute($arrSpieler['id']);

			try
			{
				$objVersions->create();
			}
			catch (\Throwable $objFehler)
			{
				// Der Datensatz ist bereits geändert; das gehört ins Protokoll
				Scope::log(
					'[Fernschach-Wartung] Für Spieler '.$arrSpieler['name'].' (ID '.$arrSpieler['id'].') konnte keine Version angelegt werden: '.$objFehler->getMessage(),
					__METHOD__,
					ContaoContext::ERROR
				);
			}

			Scope::log(
				'[Fernschach-Wartung] Mitgliedschaften von '.$arrSpieler['name'].' (ID '.$arrSpieler['id'].') in die Speicherform JJJJMMTT umgeschrieben.',
				__METHOD__,
				ContaoContext::GENERAL
			);

			++$intGeaendert;
		}

		return $intGeaendert;
	}

	/**
	 * Baut die Übersicht der gefundenen Datensätze.
	 *
	 * @param array $arrBetroffen Ergebnis aus suchen()
	 *
	 * @return string Die fertige HTML-Ausgabe
	 */
	protected function bericht($arrBetroffen)
	{
		$strAusgabe = '<div id="tl_buttons">';
		$strAusgabe .= '<a href="'.StringUtil::ampersand(self::rueckweg()).'" class="header_back" title="Zurück zur Spielerliste">Zurück</a>';
		$strAusgabe .= '</div>';

		$strAusgabe .= '<h2 class="sub_headline">Mitgliedschaften prüfen</h2>';

		if (!$arrBetroffen)
		{
			$strAusgabe .= '<div class="tl_message"><p class="tl_confirm">Alle Mitgliedschaften stehen in der Speicherform JJJJMMTT. Es ist nichts zu tun.</p></div>';

			return $strAusgabe;
		}

		$intZeilen = 0;

		foreach ($arrBetroffen as $arrSpieler)
		{
			$intZeilen += \count($arrSpieler['zeilen']);
		}

		$strAusgabe .= '<div class="tl_message"><p class="tl_info">';
		$strAusgabe .= \count($arrBetroffen).' Spieler mit zusammen '.$intZeilen.' Mitgliedschaften stehen in der Anzeigeform TT.MM.JJJJ statt in der Speicherform JJJJMMTT. ';
		$strAusgabe .= 'Beim Lesen werden sie seit Version 2.9.2 richtig ausgewertet; das Bereinigen schreibt sie zusätzlich in der Datenbank um. ';
		$strAusgabe .= 'Vor jeder Änderung entsteht eine Version, der alte Stand lässt sich also zurückholen.';
		$strAusgabe .= '</p></div>';

		$strAusgabe .= '<table class="tl_listing showColumns">';
		$strAusgabe .= '<tbody><tr>';
		$strAusgabe .= '<th class="tl_folder_tlist">BdF-Nr.</th>';
		$strAusgabe .= '<th class="tl_folder_tlist">Name</th>';
		$strAusgabe .= '<th class="tl_folder_tlist">gespeichert</th>';
		$strAusgabe .= '<th class="tl_folder_tlist">wird zu</th>';
		$strAusgabe .= '</tr>';

		$strWechsel = 'odd';

		foreach ($arrBetroffen as $arrSpieler)
		{
			foreach ($arrSpieler['zeilen'] as $intNummer => $arrZeile)
			{
				$strWechsel = 'odd' === $strWechsel ? 'even' : 'odd';
				$strAusgabe .= '<tr class="'.$strWechsel.'">';
				$strAusgabe .= '<td class="tl_file_list">'.($intNummer ? '' : StringUtil::specialchars((string) $arrSpieler['memberId'])).'</td>';
				$strAusgabe .= '<td class="tl_file_list">'.($intNummer ? '' : StringUtil::specialchars($arrSpieler['name'])).'</td>';
				$strAusgabe .= '<td class="tl_file_list">'.StringUtil::specialchars($arrZeile['alt']).'</td>';
				$strAusgabe .= '<td class="tl_file_list">'.StringUtil::specialchars($arrZeile['neu']).'</td>';
				$strAusgabe .= '</tr>';
			}
		}

		$strAusgabe .= '</tbody></table>';

		$strFrage = \count($arrBetroffen).' Spielerdatensätze jetzt bereinigen?';
		$strZiel = StringUtil::ampersand(Environment::get('request')).'&amp;bereinigen=1';

		$strAusgabe .= '<div class="tl_submit_container" style="margin-top:18px">';
		$strAusgabe .= '<a href="'.$strZiel.'" class="tl_submit" style="display:inline-block" onclick="return confirm(\''.$strFrage.'\')">Jetzt bereinigen</a>';
		$strAusgabe .= '</div>';

		return $strAusgabe;
	}

	/**
	 * Liefert die Adresse zurück zur Spielerliste.
	 *
	 * @return string Die aktuelle Adresse ohne die Parameter dieser Routine
	 */
	protected function rueckweg()
	{
		return preg_replace('/&(amp;)?(key|bereinigen)=[^&]*/', '', Environment::get('request'));
	}
}
