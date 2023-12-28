<?php

namespace Schachbulle\ContaoFernschachBundle\Modules;

/**
 * Class ZeigeTurniere
 */
class ZeigeTurniere extends \BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_turnierespieler';

	/**
	 * Zeigt die Anmeldungen eines Spielers
	 */
	protected function compile()
	{
		\System::loadLanguageFile('tl_fernschach_turniere_spieler');

		$id = \Input::get('id');

		if($id)
		{
			// Datensatz der Meldung laden
			$objMeldung = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE id=?")
			                                      ->execute($id);
			// Datensatz des Turnieres der Meldung laden
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id=?")
			                                      ->execute($objMeldung->pid);
			// Datensatz des Spielers der Meldung laden
			$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id=?")
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

			// Kontostand laden
			$salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($objMeldung->spielerId);
			$value = end($salden);
			$wert = str_replace('.', ',', sprintf('%0.2f', $value));
			if($value >= 0)
			{
				$saldo = '<span style="color:green;">';
				$saldo .= $wert.' €';
				$saldo .= '<span>';
			}
			elseif($value < 0)
			{
				$saldo = '<span style="color:red;">';
				$saldo .= $wert.' €';
				$saldo .= '<span>';
			}
			
			// Template füllen
			$this->Template->href = $this->getReferer(true);
			$this->Template->title = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
			$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
			$this->Template->Meldung = $objMeldung;
			$this->Template->Turnier = $objTurnier;
			$this->Template->Spieler = $objSpieler;
			$this->Template->Turniere = $anmeldungen_bewerbungen;
			$this->Template->Saldo = $saldo;
		}
		
		return;
	}

}
