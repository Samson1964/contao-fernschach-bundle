<?php
namespace Schachbulle\ContaoFernschachBundle\Hooks;

class Template
{

	/**
	 * BackendTemplate: Ändert die Überschrift main_headline im Template be_main
	 */
	public function BackendTemplate($strContent, $strTemplate)
	{
		// Prüfen, ob es ein das Template be_main ist
		if($strTemplate == 'be_main')
		{
			$do = \Input::get('do');
			$table = \Input::get('table');
			$id = \Input::get('id');
			
			if($do == 'fernschach-spieler' && $table == 'tl_fernschach_spieler_konto')
			{
				\System::loadlanguagefile('tl_fernschach_spieler', 'de');
				// Buchungen wurden aufgerufen, zur Überschrift den Spielernamen hinzufügen
				$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
				                                      ->execute($id);
				$str = '';
				if($objSpieler->numRows)
				{
					$str = ' &raquo; Hauptkonto von '.$objSpieler->vorname.' '.$objSpieler->nachname;
				}
				$suchen = array
				(
					'~\<h1 id=\"main_headline\"><span>(.*?)</span></h1>~s',
					'~\<div class="tl_listing_container list_view" id="tl_listing">~s',
					'~\<p class="tl_empty">Keine Einträge gefunden.</p>~s',
				);
				$ersetzen = array
				(
					'<h1 id="main_headline"><span>$1'.$str.'</span></h1>',
					'<div class="tl_listing_container list_view" id="tl_listing"><div class="tl_header click2edit toggle_select hover-div"><table class="tl_header_table">
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'][0].':</span></td>
					  <td><b>'.$objSpieler->vorname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'][0].':</span></td>
					  <td><b>'.$objSpieler->nachname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Andere Konten:</span></td>
					  <td><a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto', 'tl_fernschach_spieler_konto_nenngeld', \Environment::get('request'))).'">Nenngeldkonto</a> | <a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto', 'tl_fernschach_spieler_konto_beitrag', \Environment::get('request'))).'">Beitragskonto</a></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Hinweise:</span></td>
					  <td>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'].'</td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Aktionen:</span></td>
					  <td><a style="color:blue;" href="'.\Controller::addToUrl('key=move&rt='.REQUEST_TOKEN).'" onclick="if(!confirm(\'Verschiebung der Buchungen starten?\'))return false;Backend.getScrollOffset()">Buchungen verschieben</a>:<br>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verschieben'].'</td>
					</tr>
					</table><br></div>',
					'<div class="tl_listing_container list_view" id="tl_listing"><div class="tl_header click2edit toggle_select hover-div"><table class="tl_header_table">
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'][0].':</span></td>
					  <td><b>'.$objSpieler->vorname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'][0].':</span></td>
					  <td><b>'.$objSpieler->nachname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Andere Konten:</span></td>
					  <td><a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto', 'tl_fernschach_spieler_konto_nenngeld', \Environment::get('request'))).'">Nenngeldkonto</a> | <a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto', 'tl_fernschach_spieler_konto_beitrag', \Environment::get('request'))).'">Beitragskonto</a></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Hinweise:</span></td>
					  <td>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'].'</td>
					</tr>
					</table><br></div>
					<p class="tl_empty">Keine Einträge gefunden.</p>'
				);
				$strContent = preg_replace($suchen, $ersetzen, $strContent);
			}
			elseif($do == 'fernschach-spieler' && $table == 'tl_fernschach_spieler_konto_nenngeld')
			{
				\System::loadlanguagefile('tl_fernschach_spieler', 'de');
				// Buchungen wurden aufgerufen, zur Überschrift den Spielernamen hinzufügen
				$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
				                                      ->execute($id);
				$str = '';
				if($objSpieler->numRows)
				{
					$str = ' &raquo; Nenngeldkonto von '.$objSpieler->vorname.' '.$objSpieler->nachname;
				}
				$suchen = array
				(
					'~\<h1 id=\"main_headline\"><span>(.*?)</span></h1>~s',
					'~\<div class="tl_listing_container list_view" id="tl_listing">~s',
					'~\<p class="tl_empty">Keine Einträge gefunden.</p>~s',
				);
				$ersetzen = array
				(
					'<h1 id="main_headline"><span>$1'.$str.'</span></h1>',
					'<div class="tl_listing_container list_view" id="tl_listing"><div class="tl_header click2edit toggle_select hover-div"><table class="tl_header_table">
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'][0].':</span></td>
					  <td><b>'.$objSpieler->vorname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'][0].':</span></td>
					  <td><b>'.$objSpieler->nachname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Andere Konten:</span></td>
					  <td><a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_nenngeld', 'tl_fernschach_spieler_konto', \Environment::get('request'))).'">Hauptkonto</a> | <a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_nenngeld', 'tl_fernschach_spieler_konto_beitrag', \Environment::get('request'))).'">Beitragskonto</a></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Hinweise:</span></td>
					  <td>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'].'</td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Aktionen:</span></td>
					  <td><a style="color:blue;" href="'.\Controller::addToUrl('key=move&rt='.REQUEST_TOKEN).'" onclick="if(!confirm(\'Verschiebung der Buchungen starten?\'))return false;Backend.getScrollOffset()">Buchungen verschieben</a>:<br>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verschieben'].'</td>
					</tr>
					</table><br></div>',
					'<div class="tl_listing_container list_view" id="tl_listing"><div class="tl_header click2edit toggle_select hover-div"><table class="tl_header_table">
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'][0].':</span></td>
					  <td><b>'.$objSpieler->vorname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'][0].':</span></td>
					  <td><b>'.$objSpieler->nachname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Andere Konten:</span></td>
					  <td><a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_nenngeld', 'tl_fernschach_spieler_konto', \Environment::get('request'))).'">Hauptkonto</a> | <a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_nenngeld', 'tl_fernschach_spieler_konto_beitrag', \Environment::get('request'))).'">Beitragskonto</a></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Hinweise:</span></td>
					  <td>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'].'</td>
					</tr>
					</table><br></div>
					<p class="tl_empty">Keine Einträge gefunden.</p>',
				);
				$strContent = preg_replace($suchen, $ersetzen, $strContent);
			}
			elseif($do == 'fernschach-spieler' && $table == 'tl_fernschach_spieler_konto_beitrag')
			{
				\System::loadlanguagefile('tl_fernschach_spieler', 'de');
				// Buchungen wurden aufgerufen, zur Überschrift den Spielernamen hinzufügen
				$objSpieler = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE id = ?")
				                                      ->execute($id);
				$str = '';
				if($objSpieler->numRows)
				{
					$str = ' &raquo; Beitragskonto von '.$objSpieler->vorname.' '.$objSpieler->nachname;
				}
				$suchen = array
				(
					'~\<h1 id=\"main_headline\"><span>(.*?)</span></h1>~s',
					'~\<div class="tl_listing_container list_view" id="tl_listing">~s',
					'~\<p class="tl_empty">Keine Einträge gefunden.</p>~s',
				);
				$ersetzen = array
				(
					'<h1 id="main_headline"><span>$1'.$str.'</span></h1>',
					'<div class="tl_listing_container list_view" id="tl_listing"><div class="tl_header click2edit toggle_select hover-div"><table class="tl_header_table">
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'][0].':</span></td>
					  <td><b>'.$objSpieler->vorname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'][0].':</span></td>
					  <td><b>'.$objSpieler->nachname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Andere Konten:</span></td>
					  <td><a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_beitrag', 'tl_fernschach_spieler_konto', \Environment::get('request'))).'">Hauptkonto</a> | <a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_beitrag', 'tl_fernschach_spieler_konto_nenngeld', \Environment::get('request'))).'">Nenngeldkonto</a></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Hinweise:</span></td>
					  <td>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'].'</td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Aktionen:</span></td>
					  <td><a style="color:blue;" href="'.\Controller::addToUrl('key=move&rt='.REQUEST_TOKEN).'" onclick="if(!confirm(\'Verschiebung der Buchungen starten?\'))return false;Backend.getScrollOffset()">Buchungen verschieben</a>:<br>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verschieben'].'</td>
					</tr>
					</table><br></div>',
					'<div class="tl_listing_container list_view" id="tl_listing"><div class="tl_header click2edit toggle_select hover-div"><table class="tl_header_table">
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'][0].':</span></td>
					  <td><b>'.$objSpieler->vorname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'][0].':</span></td>
					  <td><b>'.$objSpieler->nachname.'</b></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Andere Konten:</span></td>
					  <td><a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_beitrag', 'tl_fernschach_spieler_konto', \Environment::get('request'))).'">Hauptkonto</a> | <a style="color:blue;" href="'.(str_replace('tl_fernschach_spieler_konto_beitrag', 'tl_fernschach_spieler_konto_nenngeld', \Environment::get('request'))).'">Nenngeldkonto</a></td>
					</tr>
					<tr>
					  <td valign="top"><span class="tl_label">Hinweise:</span></td>
					  <td>'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'].'</td>
					</tr>
					</table><br></div>
					<p class="tl_empty">Keine Einträge gefunden.</p>'
				);
				$strContent = preg_replace($suchen, $ersetzen, $strContent);
			}
		}
		
		return $strContent;

	}
}
