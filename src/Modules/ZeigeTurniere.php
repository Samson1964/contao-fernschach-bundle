<?php

namespace Schachbulle\ContaoFernschachBundle\Modules;

use Contao\BackendModule;
use Contao\Database;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

/**
 * Backend-Ansicht: alle Anmeldungen eines Spielers.
 *
 * Wird aus der Meldungsliste heraus als eigenes Backend-Modul aufgerufen und ist
 * deshalb in der config.php mit hideInNavigation eingetragen.
 */
class ZeigeTurniere extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_turnierespieler';

	/**
	 * Stellt die Anmeldungen des Spielers zusammen, der zu einer Meldung gehört.
	 *
	 * Die Meldungs-ID kommt als GET-Parameter "id"; von ihr aus werden Turnier
	 * und Spieler nachgeladen. Fehlt die ID, bleibt die Ansicht leer — die
	 * Template-Variablen werden trotzdem gesetzt, weil das Template sonst unter
	 * PHP 8 mit Warnungen um sich wirft.
	 *
	 * @return void Die Ausgabe entsteht über $this->Template
	 */
	protected function compile()
	{
		System::loadLanguageFile('tl_fernschach_turniere_spieler');

		$id = Input::get('id');

		// Voreinstellung, damit das Template auch ohne Meldung durchläuft
		$this->Template->href = $this->getReferer(true);
		$this->Template->title = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle'] ?? '');
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'] ?? '';
		$this->Template->Meldung = null;
		$this->Template->Turnier = null;
		$this->Template->Spieler = null;
		$this->Template->Spielerlink = '';
		$this->Template->Turniere = array();
		$this->Template->Saldo = '';

		if($id)
		{
			// Datensatz der Meldung laden
			$objMeldung = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE id=?")
			                                      ->execute($id);
			// Datensatz des Turnieres der Meldung laden
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id=?")
			                                      ->execute($objMeldung->pid);
			// Datensatz des Spielers der Meldung laden
			$objSpieler = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id=?")
			                                      ->execute($objMeldung->spielerId);

			// Anmeldungen und Bewerbungen laden
			$anmeldungen_bewerbungen = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getAnmeldungenBewerbungen($objMeldung->spielerId);
			// Aktuelle Anmeldung markieren
			for($x = 0; $x < count($anmeldungen_bewerbungen); $x++)
			{
				if($anmeldungen_bewerbungen[$x]['typ'] == 'Anmeldung' && $anmeldungen_bewerbungen[$x]['id'] == $id)
				{
					$anmeldungen_bewerbungen[$x]['class'] = 'farbe_markiert';
				}
				else
				{
					$anmeldungen_bewerbungen[$x]['class'] = '';
				}
			}

			// Kontostand laden. end() liefert bei einem leeren Feld false,
			// deshalb der Umweg über (float) — sonst stünde in der Ausgabe
			// nichts und sprintf() bekäme unter PHP 8 den falschen Typ.
			$salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($objMeldung->spielerId);
			$value = (float) end($salden);
			$wert = str_replace('.', ',', sprintf('%0.2f', $value));
			$saldo = '<span style="color:'.($value >= 0 ? 'green' : 'red').';">'.$wert.' €</span>';

			// Spieler verlinken
			$linkprefix = System::getContainer()->get('router')->generate('contao_backend');
			$spielerlink = ' <a style="color:blue;" href="'.$linkprefix.'?do=fernschach-spieler&amp;act=edit&amp;id='.$objSpieler->id.'&amp;popup=1&amp;rt='.Scope::getRequestToken().' " onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Spieler bearbeiten\',\'url\':this.href});return false">'.$objSpieler->nachname.', '.$objSpieler->vorname.'</a>';
			
			// Template füllen
			$this->Template->Meldung = $objMeldung;
			$this->Template->Turnier = $objTurnier;
			$this->Template->Spieler = $objSpieler;
			$this->Template->Spielerlink = $spielerlink;
			$this->Template->Turniere = $anmeldungen_bewerbungen;
			$this->Template->Saldo = $saldo;
		}
	}

}
