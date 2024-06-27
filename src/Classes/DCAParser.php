<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class DCAParser extends \Backend
{

	protected $dca;
	protected $backlink;
	protected $formId = null;

	public function __construct($formId)
	{
		$this->formId = $formId;
		$this->dca = array();
	}

	/**
	 * Funktion checkMembership
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public function setDCA($dca)
	{
		$this->dca = $dca;
	}

	public function setBacklink($url)
	{
		$this->backlink = $url;
	}

	public function parse()
	{
		$content = '';
		$content .= '<div class="content">';
		$content .= '  <div id="tl_buttons">';
		$content .= '    <a href="'.$this->backlink.'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b" onclick="Backend.getScrollOffset()">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>';
		$content .= '  </div>';
		$content .= '  <form id="'.$this->formId.'" class="tl_form tl_edit_form" method="post" enctype="application/x-www-form-urlencoded">';
		$content .= '    <div class="tl_formbody_edit">';
		$content .= '      <input type="hidden" name="FORM_SUBMIT" value="'.$this->formId.'">';
		$content .= '      <input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">';
		if(isset($this->dca['info']))
		{
			$content .= '      <div class="long widget">';
			$content .= '      <h2 style="margin:10px 0 10px 0; font-size:20px;">'.$this->dca['info'][0].'</h2>';
			$content .= '      <p>'.$this->dca['info'][1].'</p>';
			$content .= '      </div>';
		}
		foreach($this->dca['fieldsets'] as $key => $value)
		{
			$content .= '      <fieldset id="pal_'.$key.'" class="tl_tbox">';
			$content .= '        <legend onclick="AjaxRequest.toggleFieldset(this,\''.$key.'\',\'tl_dcaform\')">'.$this->dca['fieldsets'][$key]['title'].'</legend>';
			foreach($this->dca['fieldsets'][$key]['fields'] as $field => $value)
			{
				$content .= self::fieldParser($this->dca['fieldsets'][$key]['fields'][$field], $field);
			}
			$content .= '      </fieldset>';
		}
		$content .= '    </div>';
		$content .= '    <div class="tl_formbody_submit">';
		$content .= '      <div class="tl_submit_container">';
		$content .= '        <button type="submit" name="save" id="save" class="tl_submit" accesskey="s">'.$this->dca['submit'].'</button>';
		$content .= '      </div>';
		$content .= '    </div>';
		$content .= '  </form>';
		$content .= '</div>';

		return $content;
	}

	public function fieldParser($arrField, $fieldname)
	{
		$content = '';

		// Container des Feldes beginnen
		$content .= '<div class="'.$arrField['eval']['tl_class'].'">';

		// Pflichtfeld?
		if(isset($arrField['eval']['mandatory']) && $arrField['eval']['mandatory'])
		{
			$content .= '<h3><label for="ctrl_'.$fieldname.'"><span class="invisible">Pflichtfeld </span>'.$arrField['label'][0].'<span class="mandatory">*</span></label></h3>';
		}
		else
		{
			if(isset($arrField['inputType']))
				$content .= '<h3><label for="ctrl_'.$fieldname.'">'.$arrField['label'][0].'</label></h3>';
			else
				$content .= '<h3><label style="font-size:20px;" for="ctrl_'.$fieldname.'">'.$arrField['label'][0].'</label></h3>';
		}

		// Formularfeld anlegen
		if(isset($arrField['inputType']) && $arrField['inputType'] == 'text')
		{
			if(isset($arrField['eval']['mandatory']) && $arrField['eval']['mandatory']) $required = 'required="" ';
			else $required = '';
			$content .= '<input type="text" name="'.$fieldname.'" id="ctrl_'.$fieldname.'" class="tl_text" value="" '.$required.'onfocus="Backend.getScrollOffset()">';
			if(isset($arrField['eval']['datepicker']) == true)
			{
				$content .= '<img src="/assets/datepicker/images/icon.svg" width="20" height="20" alt="" title="Datum auswählen" id="toggle_'.$fieldname.'" style="cursor:pointer">';
				$content .= '<script>';
				$content .= '  window.addEvent("domready", function() {';
				$content .= '    new Picker.Date($("ctrl_'.$fieldname.'"), {';
				$content .= '      draggable: false,';
				$content .= '      toggle: $("toggle_'.$fieldname.'"),';
				$content .= '      format: "%d.%m.%Y",';
				$content .= '      positionOffset: {x:-211,y:-209},';
				$content .= '      timePicker: false,';
				$content .= '      pickerClass: "datepicker_bootstrap",';
				$content .= '      useFadeInOut: !Browser.ie,';
				$content .= '      startDay: 1,';
				$content .= '      titleFormat: "%d. %B %Y"';
				$content .= '    });';
				$content .= '  });';
				$content .= '</script>';
			}
		}
		elseif(isset($arrField['inputType']) && $arrField['inputType'] == 'select')
		{
			if(isset($arrField['eval']['chosen']) == true) 
				$content .= '<select name="'.$fieldname.'" id="ctrl_'.$fieldname.'" class="tl_select tl_chosen" onfocus="Backend.getScrollOffset()">';
			else
				$content .= '<select name="'.$fieldname.'" id="ctrl_'.$fieldname.'" class="tl_select" onfocus="Backend.getScrollOffset()">';
			if(isset($arrField['eval']['includeBlankOption']) == true)
			{
				$content .= '<option value="">-</option>';
			}
			// Optionen übergeben
			foreach($arrField['options'] as $key => $value)
			{
				$content .= '<option value="'.$key.'">'.$value.'</option>';
			}
			$content .= '</select>';
		}

		// Hilfetext anzeigen
		if(isset($arrField['inputType']))
			$content .= '<p class="tl_help tl_tip" title="">'.$arrField['label'][1].'</p>';
		else
			$content .= '<p style="margin-top:20px;">'.$arrField['label'][1].'</p>';
		
		// Container des Feldes beenden
		$content .= '</div>';

		return $content;
	}

	/**
	 * Returns true if the formular has been submitted
	 * Consider that $_POST-Valuas gets passed to the Widgets only on validat()
	 * @return bool
	 */
	public function isSubmitted()
	{
		return (\Input::post('FORM_SUBMIT') == $this->formId);
	}

	/**
	 * Return all data as array
	 * @return array
	 */
	public function getData()
	{
		$arrData = array();
		foreach($this->dca['fieldsets'] as $key => $value)
		{
			foreach($this->dca['fieldsets'][$key]['fields'] as $field => $value)
			{
				$arrData[$field] = \Input::post($field);
			}
		}
		$arrData['FORM_SUBMIT'] = \Input::post('FORM_SUBMIT');
		return $arrData;
	}

}
