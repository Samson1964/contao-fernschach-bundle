<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\DataContainer;
use Contao\Email;
use Contao\Environment;
use Contao\Input;
use Contao\StringUtil;

/**
 * Alles, was zum Tod eines Mitglieds gehört.
 *
 * Wird bei einem Spieler „Verstorben" gesetzt, hängen daran drei Dinge: Der
 * Todestag ist Pflicht, der Schatzmeister muss es erfahren, und die laufende
 * Mitgliedschaft endet mit dem Todestag.
 *
 * Die beiden ersten Punkte erledigt das Backend beim Speichern
 * (@see benachrichtigeSchatzmeister), den dritten der tägliche Cronjob
 * @see \Schachbulle\ContaoFernschachBundle\Cron\Todesfallpruefung. Beide
 * greifen auf die statischen Methoden dieser Klasse zu, damit die Regel nur an
 * einer Stelle steht.
 *
 * Dazu kommt der Bericht im Backend-Modul *Spieler*: Er zeigt die Altfälle, bei
 * denen ein Todesvermerk ohne Todestag gespeichert wurde. Solche Datensätze
 * kann keine Routine reparieren — das Datum kennt nur ein Mensch.
 */
class Todesfall extends Backend
{
	/**
	 * Wert des Parameters `key`, unter dem der Bericht erreichbar ist.
	 */
	private const AKTION = 'pruefeVerstorbene';

	/**
	 * Status, der einer durch den Tod beendeten Mitgliedschaft gegeben wird.
	 */
	public const STATUS = 'Verstorben';

	/**
	 * Erlaubt das Erzeugen der Klasse durch Contao.
	 *
	 * Contao ruft sowohl die Backend-Routine als auch den save_callback über
	 * import() beziehungsweise importStatic() auf, und beides erzeugt die Klasse
	 * mit `new`. In Contao 4.13 ist der Konstruktor von Backend aber geschützt —
	 * ohne diesen eigenen Konstruktor bricht der Aufruf dort mit „Call to
	 * protected Contao\Backend::__construct()" ab.
	 */
	public function __construct()
	{
	}

	/**
	 * Informiert den Schatzmeister, sobald ein Todestag eingetragen wird.
	 *
	 * Hängt als save_callback am Feld `deathday`. Der Vergleich mit dem
	 * gespeicherten Wert findet vor dem Schreiben statt: Contao ruft die
	 * save_callbacks ab, bevor der Datensatz in die Datenbank geht, ein
	 * SELECT liefert hier also noch den alten Stand. Verschickt wird deshalb
	 * genau einmal — beim ersten Eintrag, nicht bei jedem weiteren Speichern.
	 *
	 * Eine spätere Korrektur des Datums löst bewusst keine zweite E-Mail aus;
	 * dasselbe gilt für Datensätze, die über den Import ins Haus kommen. Fehlt
	 * die Adresse des Schatzmeisters in den Einstellungen, wird nichts
	 * verschickt, sondern protokolliert.
	 *
	 * @param mixed         $varValue Der neue Todestag als JJJJMMTT, 0 wenn geleert
	 * @param DataContainer $dc       Der Datensatz, an dem gespeichert wird
	 *
	 * @return mixed Der unveränderte Wert — die Methode schreibt selbst nichts
	 */
	public function benachrichtigeSchatzmeister($varValue, DataContainer $dc)
	{
		$intNeu = (int) $varValue;

		if (!$intNeu || !$dc->id)
		{
			return $varValue;
		}

		$objSpieler = Database::getInstance()->prepare('SELECT id, nachname, vorname, memberId, deathday, deathplace FROM tl_fernschach_spieler WHERE id = ?')
		                                      ->execute($dc->id);

		// Nur der erste Eintrag löst eine Nachricht aus
		if (!$objSpieler->numRows || (int) $objSpieler->deathday === $intNeu || (int) $objSpieler->deathday > 0)
		{
			return $varValue;
		}

		self::melde($objSpieler, $intNeu);

		return $varValue;
	}

	/**
	 * Schickt die Nachricht über einen Todesfall an den Schatzmeister.
	 *
	 * @param object $objSpieler Spielerdatensatz mit Name, BdF-Nummer und Sterbeort
	 * @param int    $intTodestag Todestag als JJJJMMTT
	 *
	 * @return bool True, wenn die Nachricht auf den Weg gebracht wurde. False,
	 *              wenn keine Adresse hinterlegt ist oder der Versand scheiterte;
	 *              in beiden Fällen steht der Grund im Systemprotokoll
	 */
	public static function melde($objSpieler, $intTodestag)
	{
		$strName = $objSpieler->nachname.', '.$objSpieler->vorname;
		$strEmpfaenger = trim((string) Config::get('fernschach_schatzmeisterEmail'));

		if ('' === $strEmpfaenger)
		{
			Scope::log(
				'[Fernschach-Verwaltung] Todesfall '.$strName.' (ID '.$objSpieler->id.'): Der Schatzmeister konnte nicht benachrichtigt werden, weil in den Einstellungen keine E-Mail-Adresse hinterlegt ist.',
				__METHOD__,
				ContaoContext::ERROR
			);

			return false;
		}

		$strSchatzmeister = trim((string) Config::get('fernschach_schatzmeisterName'));
		$strDatum = \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($intTodestag);
		$strLink = Scope::replaceInsertTags('{{env::url}}').'/contao?do=fernschach-spieler&amp;act=edit&amp;id='.$objSpieler->id;

		$strText = '<html><body>';
		$strText .= '<p>Hallo'.($strSchatzmeister ? ' '.StringUtil::specialchars($strSchatzmeister) : '').',</p>';
		$strText .= '<p>in der Fernschach-Verwaltung wurde ein Todesfall erfasst:</p>';
		$strText .= '<ul>';
		$strText .= '<li>Name: <b>'.StringUtil::specialchars($strName).'</b></li>';
		$strText .= '<li>BdF-Nummer: <b>'.StringUtil::specialchars((string) $objSpieler->memberId).'</b></li>';
		$strText .= '<li>Todestag: <b>'.StringUtil::specialchars($strDatum).'</b></li>';

		if ($objSpieler->deathplace)
		{
			$strText .= '<li>Sterbeort: <b>'.StringUtil::specialchars($objSpieler->deathplace).'</b></li>';
		}

		$strText .= '</ul>';
		$strText .= '<p>Die laufende Mitgliedschaft wird von der Wartung mit dem Todestag beendet.</p>';
		$strText .= '<p><a href="'.$strLink.'">Datensatz im Backend öffnen</a></p>';
		$strText .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p>';
		$strText .= '</body></html>';

		try
		{
			$objEmail = new Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = Config::get('fernschach_emailAdresse');
			$objEmail->fromName = Config::get('fernschach_emailVon');
			$objEmail->subject = 'Todesfall '.$strName;
			$objEmail->html = $strText;
			$objEmail->sendTo(array($strSchatzmeister ? $strSchatzmeister.' <'.$strEmpfaenger.'>' : $strEmpfaenger));
		}
		catch (\Throwable $objFehler)
		{
			Scope::log(
				'[Fernschach-Verwaltung] Todesfall '.$strName.' (ID '.$objSpieler->id.'): Die Nachricht an den Schatzmeister ließ sich nicht verschicken: '.$objFehler->getMessage(),
				__METHOD__,
				ContaoContext::ERROR
			);

			return false;
		}

		Scope::log(
			'[Fernschach-Verwaltung] Todesfall '.$strName.' (ID '.$objSpieler->id.'): Der Schatzmeister wurde benachrichtigt.',
			__METHOD__,
			ContaoContext::GENERAL
		);

		return true;
	}

	/**
	 * Trägt den Todestag als Ende der laufenden Mitgliedschaft ein.
	 *
	 * Beendet wird ausschließlich eine noch offene Mitgliedschaft, also eine
	 * ohne Enddatum. War die Mitgliedschaft bereits beendet — jemand ist Jahre
	 * nach seinem Austritt gestorben —, bleibt sie unangetastet; eine neue
	 * anzuhängen wäre schlicht falsch. Aus demselben Grund ändert ein zweiter
	 * Aufruf nichts mehr.
	 *
	 * @param mixed $varMitgliedschaften Inhalt von tl_fernschach_spieler.memberships
	 * @param int   $intTodestag         Todestag als JJJJMMTT
	 *
	 * @return array|null Die geänderte Liste der Mitgliedschaften, oder null,
	 *                    wenn nichts zu tun war
	 */
	public static function beendeMitgliedschaft($varMitgliedschaften, $intTodestag)
	{
		$intTodestag = (int) $intTodestag;

		if (!$intTodestag)
		{
			return null;
		}

		$arrMitgliedschaften = StringUtil::deserialize($varMitgliedschaften);

		if (!\is_array($arrMitgliedschaften) || !$arrMitgliedschaften)
		{
			return null;
		}

		$blnGeaendert = false;

		foreach ($arrMitgliedschaften as $intIndex => $arrMitgliedschaft)
		{
			// Als Zahl JJJJMMTT lesen: Im Bestand stehen auch punktierte Angaben,
			// die sich sonst nie mit 0 vergleichen ließen
			if (0 !== Helper::mitgliedschaftsdatum($arrMitgliedschaft['to'] ?? 0))
			{
				continue;
			}

			$arrMitgliedschaften[$intIndex]['to'] = (string) $intTodestag;
			$arrMitgliedschaften[$intIndex]['status'] = self::STATUS;
			$blnGeaendert = true;
		}

		return $blnGeaendert ? $arrMitgliedschaften : null;
	}

	/**
	 * Sucht Spieler, bei denen „Verstorben" ohne Todestag gespeichert ist.
	 *
	 * Seit Version 2.11.0 lässt sich das im Backend nicht mehr anlegen — der
	 * Todestag ist Pflichtfeld. Im Altbestand gibt es solche Datensätze aber,
	 * und sie fallen sonst niemandem auf.
	 *
	 * @return array Liste mit 'id', 'name', 'memberId' und 'archiviert'
	 */
	public static function ohneTodestag()
	{
		$arrTreffer = array();

		$objSpieler = Database::getInstance()->prepare('SELECT id, nachname, vorname, memberId, archived FROM tl_fernschach_spieler WHERE death = ? AND deathday = ? ORDER BY nachname ASC, vorname ASC')
		                                      ->execute(1, 0);

		while ($objSpieler->next())
		{
			$arrTreffer[] = array
			(
				'id'         => (int) $objSpieler->id,
				'name'       => $objSpieler->nachname.', '.$objSpieler->vorname,
				'memberId'   => $objSpieler->memberId,
				'archiviert' => (bool) $objSpieler->archived,
			);
		}

		return $arrTreffer;
	}

	/**
	 * Sucht Verstorbene, deren Mitgliedschaft noch offen ist.
	 *
	 * Das sind die Datensätze, die der Cronjob beim nächsten Lauf schließt. Im
	 * Bericht stehen sie, damit man sieht, was noch aussteht.
	 *
	 * @return array Liste mit 'id', 'name', 'memberId' und 'todestag' (JJJJMMTT)
	 */
	public static function offeneMitgliedschaft()
	{
		$arrTreffer = array();

		$objSpieler = Database::getInstance()->prepare('SELECT id, nachname, vorname, memberId, deathday, memberships FROM tl_fernschach_spieler WHERE death = ? AND deathday > ? ORDER BY nachname ASC, vorname ASC')
		                                      ->execute(1, 0);

		while ($objSpieler->next())
		{
			if (null === self::beendeMitgliedschaft($objSpieler->memberships, $objSpieler->deathday))
			{
				continue;
			}

			$arrTreffer[] = array
			(
				'id'       => (int) $objSpieler->id,
				'name'     => $objSpieler->nachname.', '.$objSpieler->vorname,
				'memberId' => $objSpieler->memberId,
				'todestag' => (int) $objSpieler->deathday,
			);
		}

		return $arrTreffer;
	}

	/**
	 * Zeigt den Bericht über die Todesfälle im Backend.
	 *
	 * Verändert wird nichts: Ein fehlender Todestag lässt sich nicht errechnen,
	 * und die offenen Mitgliedschaften schließt der Cronjob. Der Bericht
	 * verlinkt jeden Datensatz, damit sich das Fehlende gleich nachtragen lässt.
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

		$arrOhneDatum = self::ohneTodestag();
		$arrOffen = self::offeneMitgliedschaft();

		$strAusgabe = '<div id="tl_buttons">';
		$strAusgabe .= '<a href="'.StringUtil::ampersand(self::rueckweg()).'" class="header_back" title="Zurück zur Spielerliste">Zurück</a>';
		$strAusgabe .= '</div>';

		$strAusgabe .= '<h2 class="sub_headline">Verstorbene prüfen</h2>';

		$strAusgabe .= self::abschnitt(
			'Verstorben, aber ohne Todestag',
			'Diese Datensätze stammen aus der Zeit, bevor der Todestag Pflichtfeld war. Ohne ihn endet die Mitgliedschaft nicht, und der Beitrag läuft weiter. Das Datum muss von Hand nachgetragen werden — notfalls als ungefähres Datum.',
			'Kein Spieler ist als verstorben ohne Todestag gespeichert.',
			$arrOhneDatum,
			false
		);

		$strAusgabe .= self::abschnitt(
			'Todestag vorhanden, Mitgliedschaft noch offen',
			'Hier fehlt das Mitgliedschaftsende. Der tägliche Cronjob trägt den Todestag als Ende ein; bis dahin gelten diese Spieler noch als Mitglied.',
			'Bei allen Verstorbenen mit Todestag ist die Mitgliedschaft beendet.',
			$arrOffen,
			true
		);

		return $strAusgabe;
	}

	/**
	 * Baut einen Abschnitt des Berichts.
	 *
	 * @param string $strTitel     Überschrift des Abschnitts
	 * @param string $strErklaerung Was der Abschnitt bedeutet und was zu tun ist
	 * @param string $strLeer      Text, wenn nichts gefunden wurde
	 * @param array  $arrTreffer   Die gefundenen Spieler
	 * @param bool   $blnTodestag  True, wenn die Spalte mit dem Todestag gefüllt werden soll
	 *
	 * @return string Die fertige HTML-Ausgabe des Abschnitts
	 */
	protected static function abschnitt($strTitel, $strErklaerung, $strLeer, $arrTreffer, $blnTodestag)
	{
		$strAusgabe = '<h3 style="margin-top:18px">'.StringUtil::specialchars($strTitel).'</h3>';

		if (!$arrTreffer)
		{
			return $strAusgabe.'<div class="tl_message"><p class="tl_confirm">'.StringUtil::specialchars($strLeer).'</p></div>';
		}

		$strAusgabe .= '<div class="tl_message"><p class="tl_info">'.\count($arrTreffer).' Spieler: '.StringUtil::specialchars($strErklaerung).'</p></div>';

		$strAusgabe .= '<table class="tl_listing showColumns">';
		$strAusgabe .= '<tbody><tr>';
		$strAusgabe .= '<th class="tl_folder_tlist">BdF-Nr.</th>';
		$strAusgabe .= '<th class="tl_folder_tlist">Name</th>';
		$strAusgabe .= '<th class="tl_folder_tlist">'.($blnTodestag ? 'Todestag' : 'Zustand').'</th>';
		$strAusgabe .= '</tr>';

		$strWechsel = 'odd';

		foreach ($arrTreffer as $arrSpieler)
		{
			$strWechsel = 'odd' === $strWechsel ? 'even' : 'odd';
			$strZiel = 'contao?do=fernschach-spieler&amp;act=edit&amp;id='.$arrSpieler['id'].'&amp;rt='.Scope::getRequestToken();

			$strAusgabe .= '<tr class="'.$strWechsel.'">';
			$strAusgabe .= '<td class="tl_file_list">'.StringUtil::specialchars((string) $arrSpieler['memberId']).'</td>';
			$strAusgabe .= '<td class="tl_file_list"><a href="'.$strZiel.'" title="Datensatz bearbeiten">'.StringUtil::specialchars($arrSpieler['name']).'</a></td>';
			$strAusgabe .= '<td class="tl_file_list">'.($blnTodestag
				? StringUtil::specialchars((string) \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($arrSpieler['todestag']))
				: (!empty($arrSpieler['archiviert']) ? 'archiviert' : 'aktiv')).'</td>';
			$strAusgabe .= '</tr>';
		}

		return $strAusgabe.'</tbody></table>';
	}

	/**
	 * Liefert die Adresse zurück zur Spielerliste.
	 *
	 * @return string Die aktuelle Adresse ohne die Parameter dieser Routine
	 */
	protected static function rueckweg()
	{
		return preg_replace('/&(amp;)?key=[^&]*/', '', Environment::get('request'));
	}
}
