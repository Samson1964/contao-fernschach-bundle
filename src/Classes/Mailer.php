<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

/**
 * Class Mailer
  */
class Mailer extends \Backend
{

	/**
	 * Versenden einer E-Mail
	 */

	public function send(\DataContainer $dc)
	{
		$this->import('BackendUser', 'User');

		// E-Mail-Datensatz einlesen
		$mail = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_mails WHERE id = ?")
		                                ->execute($dc->id);
		// Spieler-Datensatz einlesen
		$spieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
		                                   ->execute($mail->pid);

		// Template aus Datenbank laden
		$template = '';
		if($mail->template)
		{
			$result = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_mailtemplates WHERE id=?")
			                                  ->execute($mail->template);
			if($result->numRows)
			{
				$template = $result->template;
			}
		}
		// Signatur einfügen, wenn aktiviert
		$signatur = '';
		if($mail->signatur)
		{
			if($mail->signatur_text)
			{
				$signatur = $mail->signatur_text;
			}
			else
			{
				$signatur = $this->User->fernschach_signatur;
			}
		}
		$preview = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getPreview($template, $mail->content, $signatur, $spieler);

		// E-Mail versenden
		if(\Input::get('token') != '' && \Input::get('token') == $this->Session->get('tl_fernschachverwaltung_send'))
		{

			$this->Session->set('tl_fernschachverwaltung_send', null);
			$objEmail = new \Email();

			// Absender "Name <email>" in ein Array $arrFrom aufteilen
			//preg_match('~(?:([^<]*?)\s*)?<(.*)>~', LIZENZVERWALTUNG_ABSENDER, $arrFrom);

			// Empfänger-Adressen in ein Array packen
			$to = explode(',', html_entity_decode(\Input::get('an')));
			$cc = explode(',', html_entity_decode(\Input::get('cc')));
			$bcc = explode(',', html_entity_decode(\Input::get('bcc')));

			// Führende und abschließende Leerzeichen entfernen, und leere Elemente entfernen
			$to = array_filter(array_map('trim', $to));
			$cc = array_filter(array_map('trim', $cc));
			$bcc = array_filter(array_map('trim', $bcc));

			// Adressen validieren, Exception bei ungültiger Adresse
			if($to && is_array($to))
			{
				foreach($to as $email)
				{
					if(!self::validateEmail($email))
					{
						throw new \Exception(sprintf($GLOBALS['TL_LANG']['fernschachverwaltung']['emailCorrupt'], $email));
					}
				}
			}
			if($cc && is_array($cc))
			{
				foreach($cc as $email)
				{
					if(!self::validateEmail($email))
					{
						throw new \Exception(sprintf($GLOBALS['TL_LANG']['fernschachverwaltung']['emailCorrupt'], $email));
					}
				}
			}
			if($bcc && is_array($bcc))
			{
				foreach($bcc as $email)
				{
					if(!self::validateEmail($email))
					{
						throw new \Exception(sprintf($GLOBALS['TL_LANG']['fernschachverwaltung']['emailCorrupt'], $email));
					}
				}
			}

			$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
			$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
			$objEmail->subject = $mail->subject;
			$objEmail->logFile = 'fernschachverwaltung_email.log';
			$objEmail->html = $preview;
			if(isset($cc[0])) $objEmail->sendCc($cc);
			if(isset($bcc[0])) $objEmail->sendBcc($bcc);
			$objEmail->replyTo($this->User->name.' <'.$this->User->email.'>');
			$status = $objEmail->sendTo($to);
			if($status)
			{
				$sendezeit = time();
				// Header
				$header = '<b>From: '.$GLOBALS['TL_CONFIG']['fernschach_emailVon'].' &lt;'.$GLOBALS['TL_CONFIG']['fernschach_emailAdresse'].'&gt;'."\n";
				$header .= 'To: '.\Input::get('an')."\n";
				$header .= 'Reply-To: '.$this->User->name.' &lt;'.$this->User->email.'&gt;'."\n";
				$header .= 'Cc: '.\Input::get('cc')."\n";
				$header .= 'Bcc: '.\Input::get('bcc')."\n\n";
				$header .= 'Subject: '.$mail->subject."\n";
				$header .= 'Date: '.date('d.m.Y H:i:s', $sendezeit)."</b>\n\n";

				// Versanddatum in Datenbank eintragen
				$set = array
				(
					'sent_date'  => $sendezeit,
					'sent_state' => 1,
					'sent_text'  => $header.$preview
				);
				$mailstatus = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_mails %s WHERE id = ?")
				                                      ->set($set)
				                                      ->execute($dc->id);
				// Email-Versand bestätigen und weiterleiten
				\Message::addConfirmation('E-Mail versendet');
				// Zurücklink generieren, ab C4 ist das ein symbolischer Link zu "contao"
				if (version_compare(VERSION, '4.0', '>='))
				{
					$backlink = \System::getContainer()->get('router')->generate('contao_backend');
				}
				else
				{
					$backlink = 'contao/main.php';
				}
				\Controller::redirect($backlink.'?do='.\Input::get('do').'&table='.\Input::get('table').'&id='.$mail->pid);
			}
			exit;
		}

		// Absender ermitteln
		$from = htmlentities($GLOBALS['TL_CONFIG']['fernschach_emailVon'].' <'.$GLOBALS['TL_CONFIG']['fernschach_emailAdresse'].'>');
		$replyto = htmlentities($this->User->name.' <'.$this->User->email.'>');
		// E-Mail-Empfänger festlegen
		$email_an = '';
		$email_cc = '';
		$email_bcc = '';
		// 1. Spieler
		if($spieler->email1) $email_an = htmlentities($spieler->vorname.' '.$spieler->nachname.' <'.$spieler->email1.'>');
		// 2. Verband
		if($mail->copyVerband)
		{
		}
		// 3. Kopie an Benutzer
		if($mail->copyBenutzer)
		{
			$email_cc .= $replyto;
		}
		$strToken = md5(uniqid(mt_rand(), true));
		$this->Session->set('tl_fernschachverwaltung_send', $strToken);

		if($mail->sent_state)
		{
			// E-Mail wurde bereits versendet, deshalb nur E-Mail-Daten anzeigen
		$return =
		'<div id="tl_buttons">
<a href="'.$this->getReferer(true).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
'.\Message::generate().'
<div class="tl_formbody_edit tl_fernschachverwaltung_send">
<div class="tl_preview">' .nl2br($mail->sent_text). '</div>
<div class="tl_formbody_submit">
<div class="tl_submit_container">
'.($mail->sent_state ? '<span class="mandatory">'.$GLOBALS['TL_LANG']['fernschachverwaltung']['emailSended'].'</span>' : '<input type="submit" onclick="return confirm(\'Soll die E-Mail wirklich verschickt werden?\')" value="E-Mail versenden" accesskey="s" class="tl_submit" id="send">').'
</div>
</div>
</form>';
		}
		else
		{
			// E-Mail-Sendeformular anzeigen
		$return =
		'<div id="tl_buttons">
<a href="'.$this->getReferer(true).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
'.\Message::generate().'
<form action="'.TL_SCRIPT.'" id="tl_fernschachverwaltung_send" class="tl_form" method="get">
<div class="tl_formbody_edit tl_fernschachverwaltung_send">
<input type="hidden" name="do" value="' . \Input::get('do') . '">
<input type="hidden" name="table" value="' . \Input::get('table') . '">
<input type="hidden" name="key" value="' . \Input::get('key') . '">
<input type="hidden" name="id" value="' . \Input::get('id') . '">
<input type="hidden" name="token" value="' . $strToken . '">
<div class="tl_preview">
<table class="prev_header">
  <tr class="row_0">
    <td class="col_0"><b>Absender:</b></td>
    <td class="col_1">'.$from.'</td>
  </tr>
  <tr class="row_1">
    <td class="col_0"><b>Antwort an:</b></td>
    <td class="col_1">'.$replyto.'</td>
  </tr>
  <tr class="row_2">
    <td class="col_0"><b>Betreff:</b></td>
    <td class="col_1">' . $mail->subject . '</td>
  </tr>
</table>
</div>
<div class="tl_preview">' .$preview. '</div>

<div class="tl_tbox">
<div class="long widget">
  <h3><label for="ctrl_an">An<span class="mandatory">*</span></label></h3>
  <input type="text" name="an" id="ctrl_an" value="'.$email_an.'" class="tl_text" onfocus="Backend.getScrollOffset()">
  <p class="tl_help tl_tip">Pflichtfeld: Empfänger dieser E-Mail. Weitere Empfänger mit Komma trennen.</p>
</div>
<div class="long widget">
  <h3><label for="ctrl_cc">Cc</label></h3>
  <input type="text" name="cc" id="ctrl_cc" value="'.$email_cc.'" class="tl_text" onfocus="Backend.getScrollOffset()">
  <p class="tl_help tl_tip">Kopie-Empfänger dieser E-Mail. Weitere Empfänger mit Komma trennen.</p>
</div>
<div class="long widget">
  <h3><label for="ctrl_bcc">Bcc</label></h3>
  <input type="text" name="bcc" id="ctrl_bcc" value="'.$email_bcc.'" class="tl_text" onfocus="Backend.getScrollOffset()">
  <p class="tl_help tl_tip">Blindkopie-Empfänger dieser E-Mail. Weitere Empfänger mit Komma trennen.</p>
</div>
<div class="clear"></div>
</div>
</div>
<div class="tl_formbody_submit">
<div class="tl_submit_container">
'.($mail->sent_state ? '<span class="mandatory">'.$GLOBALS['TL_LANG']['fernschachverwaltung']['emailSended'].'</span>' : '<input type="submit" onclick="return confirm(\'Soll die E-Mail wirklich verschickt werden?\')" value="E-Mail versenden" accesskey="s" class="tl_submit" id="send">').'
</div>
</div>
</form>';
		}
		

		return $return;
	}

	function validateEmail($email)
	{
		// Prüfen ob Email im Format "Name <Adresse>" vorliegt, ggfs. $email ändern vor der Validierung
		preg_match('~(?:([^<]*?)\s*)?<(.*)>~', $email, $result);
		
		if(isset($result[2])) $email = $result[2];
		
		return filter_var($email, FILTER_VALIDATE_EMAIL);

	}

}
