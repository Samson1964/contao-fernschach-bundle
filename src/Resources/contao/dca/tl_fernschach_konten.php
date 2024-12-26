<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Table tl_fernschach_konten
 */
$GLOBALS['TL_DCA']['tl_fernschach_konten'] = array
(

	// Config
	'config' => array
	(
		'label'                       => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['mainTitle'],
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_fernschach_konten_buchungen'),
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			array('tl_fernschach_konten', 'addBreadcrumb')
		),
		'sql'                         => array
		(
			'keys'                    => array
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
			'mode'                    => 5,
			'fields'                  => array('sorting'),
			'icon'                    => 'pagemounts.gif',
			'paste_button_callback'   => array('tl_fernschach_konten', 'pasteTournament'),
			'panelLayout'             => 'filter,search',
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s',
			'label_callback'          => array('tl_fernschach_konten', 'addIcon')
		),
		'global_operations' => array
		(
			'toggleNodes' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['toggleAll'],
				'href'                => 'ptg=all',
				'class'               => 'header_toggle',
				'showOnSelect'        => true 
			),
			'initAccounts' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['initAccounts'],
				'icon'                => 'bundles/contaofernschach/images/initAccounts.png',
				'href'                => 'key=initAccounts',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_fernschach_konten']['initAccounts_confirm'] . '\'))return false;Backend.getScrollOffset()"',
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
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
			), 
			'editBuchungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['editBuchungen'],
				'href'                => 'table=tl_fernschach_konten_buchungen',
				'icon'                => 'bundles/contaofernschach/images/euro.png',
				'button_callback'     => array('tl_fernschach_konten', 'buchungenIcon')
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['toggle'],
				'attributes' => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'           => 'published',
					'options'         => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type', 'bewerbungErlaubt'), 
		'default'                     => '{title_legend},title,type,placeholder;{description_legend:hide},description;{balance_legend:hide},openingBalance;{publish_legend},published',
	), 

	// Subpalettes
	'subpalettes' => array
	(
		'protected'                   => 'groups',
		'bewerbungErlaubt'            => 'applications,applicationText'
	), 
	
	// Fields
	'fields' => array
	(
		'id' => array
		(
			'label'                   => array('ID'),
			'search'                  => true,
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['title'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 255,
				'decodeEntities'      => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['type'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options'],
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => true,
				'helpwizard'          => true,
				'submitOnChange'      => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(7) NOT NULL default ''"
		),
		'placeholder' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['placeholder'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'isBoolean'           => true,
				'tl_class'            => 'w50 m12'
			),
			'sql'                     => "char(1) NOT NULL default ''"
		), 
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['description'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'mandatory'           => false,
			),
			'sql'                     => "mediumtext NULL"
		),
		'openingBalance' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['openingBalance'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit',
				'mandatory'           => false,
				'tl_class'            => 'w50',
				'maxlength'           => 10
			),
			'load_callback'           => array
			(
				array('tl_fernschach_konten', 'getBetrag')
			),
			'save_callback' => array
			(
				array('tl_fernschach_konten', 'putBetrag')
			),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_konten']['published'],
			'exclude'                 => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'boolean'             => true,
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		), 
	)
);

/**
 * Class tl_fernschach_konten
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Core
 */
class tl_fernschach_konten extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Add the breadcrumb menu
	 */
	public function addBreadcrumb()
	{

		// Knoten in Session speichern
		if (isset($_GET['node']))
		{
			$this->Session->set('tl_fernschach_konten_node', $this->Input->get('node'));
			$this->redirect(preg_replace('/&node=[^&]*/', '', $this->Environment->request));
		}
		$cat = $this->Session->get('tl_fernschach_konten_node');

		// Breadcrumb-Navigation erstellen
		$breadcrumb = array();
		if($cat) // Nur bei Unterkategorien
		{
			// Kategorienbaum einschränken
			$GLOBALS['TL_DCA']['tl_fernschach_konten']['list']['sorting']['root'] = array($cat);
		
			// Infos zur aktuellen Kategorie laden
			$objActual = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_konten WHERE published = ? AND id = ?')
			                                     ->execute(1, $cat);
			$breadcrumb[] = '<img src="bundles/contaofernschach/images/ordner_gelb.png" width="18" height="18" alt=""> ' . $objActual->title;
			
			// Navigation vervollständigen
			$pid = $objActual->pid;
			while($pid > 0)
			{
				$objTemp = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_konten WHERE published = ? AND id = ?')
				                                   ->execute(1, $pid);
				$breadcrumb[] = '<img src="bundles/contaofernschach/images/ordner_gelb.png" width="18" height="18" alt=""> <a href="' . \Controller::addToUrl('node='.$objTemp->id) . '" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']).'">' . $objTemp->title . '</a>';
				$pid = $objTemp->pid;
			}
			$breadcrumb[] = '<img src="' . TL_FILES_URL . 'system/themes/' . \Backend::getTheme() . '/images/pagemounts.gif" width="18" height="18" alt=""> <a href="' . \Controller::addToUrl('node=0') . '" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['selectAllNodes']).'">' . $GLOBALS['TL_LANG']['MSC']['filterAll'] . '</a>';
		}
		$breadcrumb = array_reverse($breadcrumb);

		// Insert breadcrumb menu
		if($breadcrumb)
		{
			$GLOBALS['TL_DCA']['tl_fernschach_konten']['list']['sorting']['breadcrumb'] .= '
			<ul id="tl_breadcrumb">
				<li>' . implode(' &gt; </li><li>', $breadcrumb) . '</li>
			</ul>';
		}
	}

	/**
	 * Add an image to each page in the tree
	 *
	 * @param array         $row
	 * @param string        $label
	 * @param DataContainer $dc
	 * @param string        $imageAttribute
	 * @param boolean       $blnReturnImage
	 * @param boolean       $blnProtected
	 *
	 * @return string
	 */
	public function addIcon($row, $label, DataContainer $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false)
	{
		if ($blnProtected)
		{
			$row['protected'] = true;
		}

		if($row['placeholder'])
		{
			// Platzhalter-Konto
			$image = $row['published'] ? 'bundles/contaofernschach/images/bank4.png' : 'bundles/contaofernschach/images/bank4_.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/bank4.png" data-icon-disabled="bundles/contaofernschach/images/bank4_.png"');
			$label = '<strong>' . $label . '</strong>';
		}
		else
		{
			// Normales Buchungskonto
			$image = $row['published'] ? 'bundles/contaofernschach/images/geld1.png' : 'bundles/contaofernschach/images/geld1_.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/geld1.png" data-icon-disabled="bundles/contaofernschach/images/geld1_.png"');
		}

		// Return the image only
		if ($blnReturnImage)
		{
			return \Image::getHtml($image, '', $imageAttribute);
		}


		// Rückgabe der Zeile
		return \Image::getHtml($image, '', $imageAttribute) . '<a href="' . \Controller::addToUrl('node='.$row['id']) . '" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']).'"> ' . $label . '</a>'; 

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
	 * Liefert die Liste der in der aktuellen Kategorie möglichen Typen
	 * @param \DataContainer
	 * @return array
	 */
	public function getTypen(DataContainer $dc)
	{
		if($dc->activeRecord->pid == 0)
		{
			// 1. Ebene, nur Kategorien erlaubt
			unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['tournament']);
			unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['group']);
		}
		else
		{
			// 2. - x. Ebene, dann Eltern-Typ prüfen
			$objTyp = \Database::getInstance()->prepare("SELECT type FROM tl_fernschach_konten WHERE id = ?")
			                                  ->execute($dc->activeRecord->pid);
			if($objTyp->numRows)
			{
				if($objTyp->type == 'category')
				{
					// Keine Gruppen innerhalb von Kategorien
					unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['group']);
				}
				elseif($objTyp->type == 'tournament')
				{
					// Keine Kategorien und Turnier innerhalb von Turnieren
					unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['category']);
					unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['tournament']);
				}
				elseif($objTyp->type == 'group')
				{
					// Keine Kategorien, Turniere und Gruppen innerhalb von Gruppen
					unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['category']);
					unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['tournament']);
					unset($GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options']['group']);
				}
			}
		}
		return $GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options'];
	}

	/**
	 * Return the paste page button
	 *
	 * @param DataContainer $dc
	 * @param array         $row
	 * @param string        $table
	 * @param boolean       $cr
	 * @param array         $arrClipboard
	 *
	 * @return string
	 */
	public function pasteTournament(DataContainer $dc, $row, $table, $cr, $arrClipboard=null)
	{
		$disablePA = false;
		$disablePI = false;

		if(isset($row['type']) && $row['type'] == 'group')
		{
			$disablePI = true;
		}

		$return = '';

		// Return the buttons
		if(!isset($GLOBALS['TL_LANG'][$table]['pasteafter'])) $GLOBALS['TL_LANG'][$table]['pasteafter'] = array('', '');
		if(!isset($GLOBALS['TL_LANG'][$table]['pasteinto'])) $GLOBALS['TL_LANG'][$table]['pasteinto'] = array('', '');
		$imagePasteAfter = \Image::getHtml('pasteafter.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id']));
		$imagePasteInto = \Image::getHtml('pasteinto.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][1], $row['id']));

		if ($row['id'] > 0)
		{
			$return = $disablePA ? Image::getHtml('pasteafter_.svg') . ' ' : '<a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=1&amp;pid=' . $row['id'] . (!is_array($arrClipboard['id']) ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteAfter . '</a> ';
		}

		return $return . ($disablePI ? Image::getHtml('pasteinto_.svg') . ' ' : '<a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=2&amp;pid=' . $row['id'] . (!is_array($arrClipboard['id']) ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][$row['id'] > 0 ? 1 : 0], $row['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteInto . '</a> ');
	}

	/**
	 * Gibt den Button für die Bearbeitung der Bewerbungen zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function buchungenIcon($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['placeholder'])
		{
			// Keine Bearbeitung von Bewerbungen
			$icon = 'bundles/contaofernschach/images/euro_.png';
			$title = 'Keine Buchungen möglich in einem Platzhalter-Konto';
			return '<span>'.\Image::getHtml($icon, $label).'</span> ';
		}
		else
		{
			// Buchungen können bearbeitet werden
			return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> '; 
		}

	}

	/**
	 * Gibt den Button für die Bearbeitung der Bewerbungen zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function infoBewerbungen($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['bewerbungErlaubt'] && $row['type'] == 'tournament')
		{
			// Bewerbungen können bearbeitet werden, deshalb Anzahl Bewerbungen anzeigen
			if($this->bewerbungen[$row['id']])
			{
				$temp = '<span style="color:#9F9F9F;">Bewerbungen: <b>'.$this->bewerbungen[$row['id']]['anzahl'].'</b> [';
				if($this->bewerbungen[$row['id']]['unklar']) $temp .= '<span title="Anzahl der nicht geklärten Bewerbungen">'.$this->bewerbungen[$row['id']]['unklar'].$this->generateImage($this->getImage('bundles/contaofernschach/images/fragezeichen.png', 12, 12, 'proportional'), 'ohne Entscheidung').'</span> ';
				if($this->bewerbungen[$row['id']]['zusagen']) $temp .= '<span title="Anzahl der Zusagen">'.$this->bewerbungen[$row['id']]['zusagen'].$this->generateImage($this->getImage('bundles/contaofernschach/images/ja.png', 12, 12, 'proportional'), 'Zusagen').'</span> ';
				if($this->bewerbungen[$row['id']]['absagen']) $temp .= '<span title="Anzahl der Absagen">'.$this->bewerbungen[$row['id']]['absagen'].$this->generateImage($this->getImage('bundles/contaofernschach/images/nein.png', 12, 12, 'proportional'), 'Absagen').'</span> ';
				$temp = rtrim($temp).']</span><span style="width:20px; display:inline-block;"></span>';
			}
			return $temp;
		}
		else
		{
			// Keine Bearbeitung von Bewerbungen
			return '';
		}

	}

	/**
	 * Gibt den Button für die Bearbeitung der Meldungen zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function meldungenIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if($row['type'] == 'category' || $row['type'] == 'group')
		{
			// Keine Bearbeitung von Meldungen bei Kategorien und Gruppen
			$icon = 'bundles/contaofernschach/images/turnier_meldungen_inaktiv.png';
			$title = 'Keine Meldungen möglich bei diesem Eintrag';
			return '<span>'.\Image::getHtml($icon, $label).'</span> ';
		}
		else
		{
			// Meldungen können bearbeitet werden
			return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> '; 
		}

	}

	/**
	 * Gibt den Button für die Bearbeitung der Spieler zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function spielerIcon($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['type'] == 'category' || $row['type'] == 'tournament')
		{
			// Keine Bearbeitung von Spielern bei Kategorien und meldefähigen Turnieren
			$icon = 'bundles/contaofernschach/images/turnier_gruppe_inaktiv.png';
			$title = 'Keine Spieler möglich bei diesem Eintrag';
			return '<span>'.\Image::getHtml($icon, $label).'</span> ';
		}
		else
		{
			// Spieler können bearbeitet werden
			return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> '; 
		}

	}

	/**
	 * Betrag für Datenbank umwandeln
	 * @param $varValue       string      z.B. 9,12
	 * @return                float       z.B. 9.12
	 */
	public function putBetrag($varValue)
	{
		$temp = str_replace(',', '.', $varValue); // Komma in Punkt umwandeln
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

