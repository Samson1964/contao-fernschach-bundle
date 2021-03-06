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
 * Table tl_fernschach_spieler_konto
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler_konto'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_spieler',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'            => 'primary',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('datum'),
			'flag'                    => 3,
			'headerFields'            => array('nachname', 'vorname'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_fernschach_spieler_konto', 'listBuchungen'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['importBuchungen'],
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				//'button_callback'     => array('tl_fernschach_spieler_konto', 'copyArchive')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				//'button_callback'     => array('tl_fernschach_spieler_konto', 'deleteArchive')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset(); return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_fernschach_spieler_konto', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'markiertIcon' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['markiertIcon'],
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
		'default'                     => '{buchung_legend},betrag,typ,datum,kategorie,art,verwendungszweck;{extras_legend},markierung,saldoReset;{turnier_legend:hide},turnier;{comment_legend:hide},comment;{connection_legend},meldungId;{publish_legend},published'
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
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'tstamp' => array
		(
			'sorting'                 => true,
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'importDate' => array
		(
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'betrag' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['betrag'],
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
				array('tl_fernschach_spieler_konto', 'getBetrag')
			),
			'save_callback' => array
			(
				array('tl_fernschach_spieler_konto', 'putBetrag')
			),
			'sql'                     => "varchar(6) NOT NULL default ''"
		),
		'typ' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['typ_options'],
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['datum'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_spieler_konto', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'kategorie' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['kategorie'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['kategorie_options'],
			'eval'                    => array
			(                         
				'tl_class'            => 'w50 clr', 
				'multiple'            => false,
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'art' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art_options'],
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verwendungszweck'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'markierung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['markierung'],
			'inputType'               => 'checkbox',
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['saldoReset'],
			'inputType'               => 'checkbox',
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
			'options_callback'        => array('tl_fernschach_spieler_konto', 'getTurniere'),
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['meldungId'],
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['published'],
			'inputType'               => 'checkbox',
			'default'                 => 1,
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
	)
);


/**
 * Class tl_fernschach_spieler_konto
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_spieler_konto extends Backend
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

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_spieler_konto::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}

	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_spieler_konto::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht f??r Aktivierung Datensatz ID "'.$intId.'"', 'tl_fernschach_spieler_konto toggleVisibility', TL_ERROR);
			// Zur??cklink generieren, ab C4 ist das ein symbolischer Link zu "contao"
			if (version_compare(VERSION, '4.0', '>='))
			{
				$backlink = \System::getContainer()->get('router')->generate('contao_backend');
			}
			else
			{
				$backlink = 'contao/main.php';
			}
			$this->redirect($backlink.'?act=error');
		}

		$this->createInitialVersion('tl_fernschach_spieler_konto', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_spieler_konto']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_spieler_konto']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_fernschach_spieler_konto SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_fernschach_spieler_konto', $intId);
	}

	/**
	 * Datens??tze auflisten
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
		// Buchung auflisten
		$row++;
		$temp = '';
		if($row == 1)
		{
			// Salden berechnen
			self::getSaldo($arrRow['pid']);
		}
		$temp .= '<div class="tl_content_right">';
		$saldo = self::getEuro($this->salden[$arrRow['id']]);
		$temp .= '<span style="display:inline-block; width:120px;" title="Der Saldo wird aus allen ver??ffentlichten, ggfs. gefilterten Datens??tzen berechnet."><b>Saldo: '.$saldo.'</b></span> ';
		$temp .= '</div>';
		if($arrRow['markierung']) $temp .= '<div class="tl_content_left" style="background-color:#FFE8DD;">';
		else $temp .= '<div class="tl_content_left">';
		$temp .= '<span style="display:inline-block; width:100px; '.$css.'">'.date('d.m.Y', $arrRow['datum']).'</span>';
		$temp .= '<span style="display:inline-block; width:80px; text-align:right; margin-right:20px;">';
		if($arrRow['saldoReset']) $temp .= '<img title="Der Saldo wurde vor der Buchung auf 0 gesetzt." src="bundles/contaofernschach/images/reset.svg" width="16"> ';
		$temp .= self::getEuro($arrRow['betrag'], $arrRow['typ']).'</span>';
		$temp .= '<span style="display:inline-block; width:100px; '.$css.'">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art_options'][$arrRow['art']].'</span>';
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

		// Betrag formatieren und zur??ckgeben
		$value = str_replace('.', ',', sprintf('%0.2f', $value));
		return $html.$value.' ???</span>';
	}

	/**
	 * Saldorechner
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public function getSaldo($pid)
	{
		$session = $this->Session->getData(); // Sitzung laden
		$sql = ''; // SQL-String Filter und Suche initialisieren
		
		// Filter laden
		if($session['filter']['tl_fernschach_spieler_konto_'.$pid]['typ'])
		{
			$sql .= " AND typ = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['typ']."'";
		}
		if($session['filter']['tl_fernschach_spieler_konto_'.$pid]['art'])
		{
			$sql .= " AND art = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['art']."'";
		}
		if($session['filter']['tl_fernschach_spieler_konto_'.$pid]['kategorie'])
		{
			$sql .= " AND kategorie = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['kategorie']."'";
		}
		if($session['filter']['tl_fernschach_spieler_konto_'.$pid]['markieren'])
		{
			$sql .= " AND markieren = '".$session['filter']['tl_fernschach_spieler_konto_'.$pid]['markieren']."'";
		}

		//echo "<pre>";
		// Filter laden
		
		//print_r($session['filter']['tl_fernschach_spieler_konto_'.$pid]); // typ =>, art =>
		//print_r($session['search']['tl_fernschach_spieler_konto']); // field => name, value =>
		//print_r($session);
		//echo "</pre>";
		//echo $sql;
		$objBuchungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE pid=?".$sql.' ORDER BY datum ASC')
		                                        ->execute($pid);

		$saldo = 0;
		if($objBuchungen->numRows)
		{
			while($objBuchungen->next())
			{
				//echo "Betrag=".$objBuchungen->betrag." Saldo davor=".$saldo;
				if($objBuchungen->saldoReset) 
				{
					$saldo = 0; // Saldo soll hier resettet werden
					//echo " Saldo nach Reset=".$saldo;
				}
				switch($objBuchungen->typ)
				{
					case 'h':
						$saldo += (float)$objBuchungen->betrag;
						break;
					case 's':
						$saldo -= (float)$objBuchungen->betrag;
						break;
					default:
				}
				//echo " Saldo danach=".$saldo."<br>";
				// Saldo dem Salden-Array zuordnen
				$this->salden[$objBuchungen->id] = $saldo;
			}
		}
		
		// Saldo formatieren
		//echo $saldo;
		if((float)$saldo >= 0) return '<span style="color:green;">'.self::getEuro($saldo).'</span>';
		else return '<span style="color:red;">'.self::getEuro($saldo).'</span>';

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
	 * options_callback: Erm??glicht das Bef??llen eines Drop-Down-Men??s oder einer Checkbox-Liste mittels einer individuellen Funktion.
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
	 * Betrag f??r Datenbank umwandeln
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
}