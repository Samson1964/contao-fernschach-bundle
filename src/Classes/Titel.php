<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class Titel extends \Backend
{

	public function __construct()
	{
	}

	/**
	 * Lädt alle veröffentlichten Titel von veröffentlichten Spielern
	 *
	 * @param
	 *
	 * @return array
	 */
	public static function get()
	{
		// Kompatibilität mit $GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel_options'] herstellen
		$titelnamen = array
		(
			'FSGM' => 'fgm',
			'SIM'  => 'sim',
			'FSIM' => 'fim',
			'CCM'  => 'ccm',
			'LGM'  => 'lgm',
			'CCE'  => 'cce',
			'LIM'  => 'lim',
		);

		$titel = array();
		// Aktive Titel-Datensätze laden
		$objTitel = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler_titel WHERE published = ?')
		                                    ->execute(1);

		// Titel auslesen
		if($objTitel->numRows)
		{
			while($objTitel->next())
			{
				// Spielerdatensatz laden
				$objMember = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler WHERE id = ?')
				                                     ->execute($objTitel->pid);
				// Nur Titel von veröffentlichten Spielern berücksichtigen
				if($objMember->published)
				{
					$titel[] = array
					(
						'id'          => $objMember->id,
						'nachname'    => $objMember->nachname,
						'vorname'     => $objMember->vorname,
						'titel'       => isset($titelnamen[$objTitel->titel]) ? $titelnamen[$objTitel->titel] : $objTitel->titel,
						'datum'       => $objTitel->datum,
						'turnier'     => '',
						'link'        => '',
					);
				}
			}
		}
		return $titel;
	}

}
