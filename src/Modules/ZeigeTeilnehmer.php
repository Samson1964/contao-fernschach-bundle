<?php

namespace Schachbulle\ContaoFernschachBundle\Modules;

use Contao\BackendModule;
use Contao\Database;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;

/**
 * Backend-Ansicht: alle Teilnehmer eines Turniers.
 *
 * Wird aus der Turnierliste heraus als eigenes Backend-Modul aufgerufen und ist
 * deshalb in der config.php mit hideInNavigation eingetragen.
 */
class ZeigeTeilnehmer extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_turnierteilnehmer';

	/**
	 * Stellt die Teilnehmerliste eines Turniers zusammen.
	 *
	 * Die Turnier-ID kommt als GET-Parameter "id". Fehlt sie oder gibt es das
	 * Turnier nicht, bleibt die Liste leer — die Template-Variablen werden
	 * trotzdem gesetzt, weil das Template sonst unter PHP 8 mit Warnungen um
	 * sich wirft.
	 *
	 * @return void Die Ausgabe entsteht über $this->Template
	 */
	protected function compile()
	{
		System::loadLanguageFile('tl_fernschach_turniere_spieler');

		$id = Input::get('id'); // Turnier-ID laden

		// Voreinstellung, damit das Template auch ohne Turnier durchläuft
		$this->Template->href = $this->getReferer(true);
		$this->Template->title = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle'] ?? '');
		$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'] ?? '';
		$this->Template->Meldungen = array();
		$this->Template->Turnier = array
		(
			'title'              => '',
			'type'               => '',
			'kennziffer'         => '',
			'registrationDate'   => '',
			'startDate'          => '',
			'turnierleiterName'  => '',
			'turnierleiterEmail' => '',
			'archived'           => '',
			'published'          => '',
		);

		if($id)
		{
			// Turnier laden
			$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id=?")
			                                      ->execute($id);

			if($objTurnier->numRows)
			{
				$this->Template->Turnier = array
				(
					'title'              => $objTurnier->title,
					'type'               => $objTurnier->type,
					'kennziffer'         => $objTurnier->kennziffer,
					// Ohne hinterlegtes Datum stünde hier sonst der 1.1.1970
					'registrationDate'   => $objTurnier->registrationDate ? date('d.m.Y', (int) $objTurnier->registrationDate) : '',
					'startDate'          => $objTurnier->startDate ? date('d.m.Y', (int) $objTurnier->startDate) : '',
					'turnierleiterName'  => $objTurnier->turnierleiterName,
					'turnierleiterEmail' => $objTurnier->turnierleiterEmail,
					'archived'           => $objTurnier->archived,
					'published'          => $objTurnier->published
				);
			}

			// Meldungen für das Turnier laden
			$objMeldungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE player=? AND playerIn=? ORDER BY meldungDatum ASC")
			                                        ->execute(true, $id);
			
			$meldungen = array();
			if($objMeldungen->numRows)
			{
				$nummer = 0;
				while($objMeldungen->next())
				{
					$nummer++;
					$meldungen[] = array
					(
						'class'        => '',
						'nummer'       => $nummer,
						'meldungDatum' => $objMeldungen->meldungDatum ? date('d.m.Y H:i', (int) $objMeldungen->meldungDatum) : '',
						'vorname'      => $objMeldungen->vorname,
						'nachname'     => $objMeldungen->nachname,
						'mglnr'        => $objMeldungen->memberId,
					);
				}
			}

			$this->Template->Meldungen = $meldungen;
		}
	}

}
