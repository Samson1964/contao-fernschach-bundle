<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Table tl_fernschach_spieler_konto_nenngeld
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler_konto_nenngeld'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_spieler',
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			array('tl_fernschach_spieler_konto_nenngeld', 'checkPermission'),
			array('tl_fernschach_spieler_konto_nenngeld', 'checkSaldo')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'                  => 'primary',
				'pid'                 => 'index',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('datum', 'sortierung'),
			'flag'                    => 3,
			'headerFields'            => array('nachname', 'vorname'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_fernschach_spieler_konto_nenngeld', 'listBuchungen'),
			'disableGrouping'         => true
		),
		'label' => array
		(
			'fields'                  => array('betrag'),
			'format'                  => '%s',
		),
		'global_operations' => array
		(
			'importBuchungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['importBuchungen'],
				'href'                => 'key=importBuchungen',
				'icon'                => 'bundles/contaofernschach/images/import.png'
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
				'button_callback'     => array('tl_fernschach_spieler_konto_nenngeld', 'generateEditButton')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
				'button_callback'     => array('tl_fernschach_spieler_konto_nenngeld', 'generateCopyButton')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_fernschach_spieler_konto_nenngeld', 'generateDeleteButton')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['toggle'],
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'           => 'published',
					'options'         => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
				//'button_callback'     => array('tl_fernschach_spieler_konto_nenngeld', 'generateToggleButton')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['show'],
				'href'                => 'act=show',
				//'attributes'          => 'onclick="Backend.openModalIframe({\'title\':\'Details anzeigen\',\'url\':this.href});return false"',
				'icon'                => 'bundles/contaofernschach/images/show.svg',
				//'button_callback'     => array('tl_fernschach_spieler_konto_nenngeld', 'generateShowButton')
			),
			'markiertIcon' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['markiertIcon'],
				'attributes'          => 'onclick="markiertSetzen(this, %s); javascript:alert(\'Hallo\'); Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'           => 'markierung',
					'options'         => array
					(
						array('value' => '', 'icon' => 'bundles/contaofernschach/images/unfertig.png'),
						array('value' => '1', 'icon' => 'bundles/contaofernschach/images/fertig.png'),
					)
				),
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{buchung_legend},betrag,typ,datum,sortierung,art,verwendungszweck;{extras_legend},markierung,saldoReset;{turnier_legend:hide},turnier;{comment_legend:hide},comment;{connection_legend},meldungId;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_fernschach_spieler.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sorting'                 => true,
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'resetRecord' => array
		(
			'exclude'                 => true,
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'importDate' => array
		(
			'exclude'                 => true,
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'betrag' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['betrag'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit',
				'mandatory'           => false,
				'tl_class'            => 'w50',
				'maxlength'           => 6
			),
			'load_callback'           => array
			(
				array('tl_fernschach_spieler_konto_nenngeld', 'getBetrag')
			),
			'save_callback' => array
			(
				array('tl_fernschach_spieler_konto_nenngeld', 'putBetrag')
			),
			'sql'                     => "varchar(6) NOT NULL default ''"
		),
		'typ' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['typ_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'multiple'            => false,
				'mandatory'           => true,
				'includeBlankOption'  => false
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'datum' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['datum'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_spieler_konto_nenngeld', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'sortierung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['sortierung'],
			'exclude'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'tl_class'            => 'w50', 
				'maxlength'           => 5,
				'rgxp'                => 'digit'
			),
			'sql'                     => "int(5) unsigned NOT NULL default 0"
		),
		'art' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['art'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['art_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'multiple'            => false,
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'verwendungszweck' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['verwendungszweck'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'markierung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['markierung'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'saldoReset' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['saldoReset'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'turnier' => array
		(
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_fernschach_spieler_konto_nenngeld', 'getTurniere'),
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'includeBlankOption'  => true,
				'chosen'              => true,
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'comment' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'tl_class'            => 'w50 noresize',
			),
			'sql'                     => "text NULL"
		),
		'meldungId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['meldungId'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['published'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'default'                 => 1,
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
	)
);


/**
 * Class tl_fernschach_spieler_konto_nenngeld
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_spieler_konto_nenngeld extends \Backend
{

	var $turniere = array();
	var $salden = array();

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');

	}

	/**
	 * onload_callback: Fuehrt eine Aktion bei der Initialisierung des DataContainer-Objekts aus.
	 * @param $dc
	 */
	public function checkSaldo(\DataContainer $dc)
	{
		$id = strlen(Input::get('id')) ? Input::get('id') : $dc->currentPid;

		// Salden berechnen
		$this->salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($id, 'nenngeld');
		return;

	}

	/**
	 * Prüfe Zugangsrechte für tl_fernschach_spieler
	 *
	 * @throws AccessDeniedException
	 */
	public function checkPermission()
	{
		if($this->User->isAdmin)
		{
			return;
		}

		// Zugriff auf globale Operationen prüfen
		if(!$this->User->hasAccess('import', 'fernschach_konto')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler_konto_nenngeld']['list']['global_operations']['importBuchungen']);
		if(!$this->User->hasAccess('all', 'fernschach_konto')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler_konto_nenngeld']['list']['global_operations']['all']);
		if(!$this->User->hasAccess('create', 'fernschach_konto')) $GLOBALS['TL_DCA']['tl_fernschach_spieler_konto_nenngeld']['config']['closed'] = true;

		// Aktuelle Aktion von act prüfen
		switch (Input::get('act'))
		{
			case 'create': // Buchung anlegen
				if(!$this->User->hasAccess('create', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine neue Buchung anzulegen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break; 

			case 'copy': // Buchung kopieren
				if(!$this->User->hasAccess('copy', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu kopieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break; 
				
			case 'toggle': // Buchung aktivieren/deaktivieren
				if(!$this->User->hasAccess('toggle', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu (de)aktivieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break; 

			case 'show': // Infobox
				if(!$this->User->hasAccess('show', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung-Infobox anzuzeigen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break; 

			case 'edit': // Buchung bearbeiten
				if(!$this->User->hasAccess('edit', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu bearbeiten.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break; 

			default:

				// Aktuelle Aktion von key prüfen
				switch (Input::get('key'))
				{
					case 'import': // Buchungen importieren
						if(!$this->User->hasAccess('import', 'fernschach_konto'))
						{
							$this->log('Fernschach-Verwaltung: Keine Rechte, um Buchungen zu importieren.', __METHOD__, TL_ERROR);
							$this->redirect('contao/main.php?act=error');
						}
						break; 
        		
					default:
				}
		}

	}

	/**
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listBuchungen($arrRow)
	{
		static $row;
		// Buchung positiv oder negativ?
		switch($arrRow['typ'])
		{
			case 'h': $css = 'color:green;'; break;
			case 's': $css = 'color:red;'; break;
			default: $css = 'color:yellow;'; break;
		}
		
		// Resetbuchung = true (schwarze Linie unten)
		$resetCss = '';
		if($arrRow['resetRecord'])
		{
			$resetCss = 'border-bottom: 2px red solid; padding-bottom:1px;';
		}
		elseif($arrRow['saldoReset'])
		{
			$resetCss = 'border-bottom: 2px black solid; padding-bottom:1px;';
		}

		// Buchung auflisten
		$row++;
		$temp = '';
		$temp .= '<div class="tl_content_right">';
		$saldo = self::getEuro($this->salden[$arrRow['id']]);
		$temp .= '<span style="display:inline-block; width:120px;" title="Der Saldo wird aus allen veröffentlichten, ggfs. gefilterten Datensätzen berechnet."><b>Saldo: '.$saldo.'</b></span> ';
		$temp .= '</div>';

		// Markieren = true (rötliche Hintergrundfarbe)
		if($arrRow['markierung']) $temp .= '<div class="tl_content_left" style="background-color:#FFE8DD;'.$resetCss.'">';
		else $temp .= '<div class="tl_content_left" style="'.$resetCss.'">';

		$temp .= '<span style="display:inline-block; width:100px; '.$css.'">'.date('d.m.Y', $arrRow['datum']);

		// Sortierungshilsfeld gesetzt
		if($arrRow['sortierung']) $temp .= ' ('.$arrRow['sortierung'].')';

		$temp .= '</span>';
		$temp .= '<span style="display:inline-block; width:80px; text-align:right; margin-right:20px;">';
		//if($arrRow['resetRecord']) $temp .= '<img title="Diese Saldoreset-Buchung wurde global festgelegt." src="bundles/contaofernschach/images/resetGlobal.svg" width="12" align="middle"> ';
		//elseif($arrRow['saldoReset']) $temp .= '<img title="Der Saldo wurde vor der Buchung auf 0 gesetzt." src="bundles/contaofernschach/images/reset.svg" width="12" align="middle"> ';
		$temp .= self::getEuro($arrRow['betrag'], $arrRow['typ']).'</span>';
		$temp .= '<span style="display:inline-block; width:100px; '.$css.'">'.@$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['art_options'][$arrRow['art']].'</span>';
		$temp .= '<span style="display:inline-block; width:250px; '.$css.'" title="Verwendungszweck">'.$arrRow['verwendungszweck'].'</span>';
		if($arrRow['turnier'])
		{
			// Ein Turnier wurde zugewiesen
			$temp .= '<span style="display:inline-block;" title="Zugeordnetes Turnier">'.self::getTurnier($arrRow['turnier']).'</span>';
		}
		$temp .= '</div>';
		return $temp;
	}

	/**
	 * Zahl in Euro-Betrag umwandeln
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public function getEuro($value, $typ = false)
	{
		// Komma umwandeln in Punkt
		$value = str_replace(',', '.', $value);

		// Farbe bestimmen
		if($typ)
		{
			if($typ == 'h') $html = '<span style="color:green;">';
			elseif($typ == 's') $html = '<span style="color:red;">';
			else $html = '<span>';
		}
		else
		{
			if($value > 0) $html = '<span style="color:green;">';
			elseif($value < 0) $html = '<span style="color:red;">';
			else $html = '<span>';
		}

		// Betrag formatieren und zurückgeben
		$value = str_replace('.', ',', sprintf('%0.2f', $value));
		return $html.$value.' €</span>';
	}

	/**
	 * Set the timestamp to 00:00:00 (see #26)
	 *
	 * @param integer $value
	 *
	 * @return integer
	 */
	public function loadDate($value)
	{
		if($value) return strtotime(date('Y-m-d', $value) . ' 00:00:00');
		else return '';
	}

	/**
	 * options_callback: Ermöglicht das Befüllen eines Drop-Down-Menüs oder einer Checkbox-Liste mittels einer individuellen Funktion.
	 * @param  $dc
	 * @return array
	 */
	public function getTurniere(\DataContainer $dc)
	{
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere ORDER BY title ASC")
		                                       ->execute();

		$arr = array();
		if($objTurniere->numRows)
		{
			while($objTurniere->next())
			{
				$arr[$objTurniere->id] = $objTurniere->title.' (Nenngeld: '.self::getEuro($objTurniere->nenngeld).')';
			}
		}
		return $arr;
	}

	public function getTurnier($id)
	{
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
		                                       ->execute($id);

		if($objTurniere->numRows)
		{
			return $objTurniere->title;
		}
		return false;
	}

	/**
	 * Betrag für Datenbank umwandeln
	 * @param $varValue       string      z.B. 9,12
	 * @return                float       z.B. 9.12
	 */
	public function putBetrag($varValue)
	{
		$temp = str_replace(',', '.', $varValue); // Komma in Punkt umwandeln
		$temp = abs($temp); // Vorzeichen entfernen
		return $temp;
	}

	/**
	 * Betrag aus der Datenbank umwandeln
	 * @param $varValue       string      z.B. 9,12
	 * @return                float       z.B. 9.12
	 */
	public function getBetrag($varValue)
	{
		$temp = sprintf("%01.2f", $varValue); // In Wert mit zwei Nachkommastellen umwandeln
		$temp = str_replace('.', ',', $temp); // Punkt in Komma umwandeln
		return $temp;
	}

	/**
	 * Gibt den Edit-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateEditButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('edit', 'fernschach_konto')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Copy-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateCopyButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('copy', 'fernschach_konto')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Delete-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateDeleteButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('delete', 'fernschach_konto')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Toggle-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateToggleButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('toggle', 'fernschach_konto')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Show-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateShowButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('show', 'fernschach_konto')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

}