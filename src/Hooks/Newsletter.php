<?php
namespace Schachbulle\ContaoFernschachBundle\Hooks;

class Newsletter
{

	public function NewsletterTags(\Template $objTemplate)
	{
		return; // Tokens werden nicht ersetzt, und zwar auch die von Contao!
		
		// Prüfen, ob es ein Mailtemplate ist
		if(strpos($objTemplate->getName(), 'mail_') !== 0)
			return;

		$arr = array();

		// Daten des Empfängers aus dem Newsletter-Verteiler laden
		$verteiler = \Database::getInstance()->prepare('SELECT * FROM tl_newsletter_recipients WHERE pid=? AND email=?')
		                                     ->execute($GLOBALS['TL_CONFIG']['fernschach_newsletter'], $objTemplate->recipient);
		if($verteiler->numRows)
		{
			// Daten des Empfängers aus der Fernschach-Verwaltung laden
			$spieler = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_spieler WHERE id=?')
			                                   ->execute($verteiler->fernschach_id);
			if($spieler->numRows)
			{
				$suchen = array('##vorname##', '##nachname##');
				$ersetzen = array($spieler->vorname, $spieler->nachname);
				$arr = $objTemplate->getData();
				$arr['body'] = str_replace($suchen, $ersetzen, $arr['body']);
				$objTemplate->setData($arr);
			}
		}

		//log_message(print_r($arr, true), 'test.log');

	}
}
