<?php

namespace Schachbulle\ContaoFernschachBundle\Modules;

/**
 * Class ZeigeTurniere
 */
class ZeigeTeilnehmer extends \BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_turnierteilnehmer';

	/**
	 * Zeigt die Teilnehmer eines Turniers
	 */
	protected function compile()
	{
		\System::loadLanguageFile('tl_fernschach_turniere_spieler');

		$id = \Input::get('id'); // Turnier-ID laden

		if($id)
		{
			// Turnier laden
			$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id=?")
			                                      ->execute($id);

			$turnier = array();
			if($objTurnier->numRows)
			{
				$turnier = array
				(
					'title' => $objTurnier->title,
					'type' => $objTurnier->type,
					'kennziffer' => $objTurnier->kennziffer,
					'registrationDate' => date('d.m.Y', $objTurnier->registrationDate),
					'startDate' => date('d.m.Y', $objTurnier->startDate),
					'turnierleiterName' => $objTurnier->turnierleiterName,
					'turnierleiterEmail' => $objTurnier->turnierleiterEmail,
					'archived' => $objTurnier->archived,
					'published' => $objTurnier->published
				);
			}

			// Meldungen für das Turnier laden
			$objMeldungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE player=? AND playerIn=? ORDER BY meldungDatum ASC")
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
						'class' => '',
						'nummer' => $nummer,
						'meldungDatum' => date('d.m.Y H:i', $objMeldungen->meldungDatum),
						'vorname' => $objMeldungen->vorname,
						'nachname' => $objMeldungen->nachname,
						'mglnr' => $objMeldungen->memberId,
					);
				}
			}
			// Template füllen
			$this->Template->href = $this->getReferer(true);
			$this->Template->title = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
			$this->Template->button = $GLOBALS['TL_LANG']['MSC']['backBT'];
			$this->Template->Meldungen = $meldungen;
			$this->Template->Turnier = $turnier;
		}
		
		return;
	}

}
