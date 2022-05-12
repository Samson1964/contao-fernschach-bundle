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
 * Table tl_fernschach_turniere
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere'] = array
(

	// Config
	'config' => array
	(
		'label'                       => $GLOBALS['TL_LANG']['tl_fernschach_turniere']['mainTitle'],
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_fernschach_turniere_meldungen', 'tl_fernschach_turniere_spieler', 'tl_fernschach_turniere_bewerbungen'),
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			array('tl_fernschach_turniere', 'addBreadcrumb')
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
			'paste_button_callback'   => array('tl_fernschach_turniere', 'pasteTournament'),
			'panelLayout'             => 'filter,search',
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s',
			'label_callback'          => array('tl_fernschach_turniere', 'addIcon')
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
			'infoBewerbungen' => array
			(
				'button_callback'     => array('tl_fernschach_turniere', 'infoBewerbungen')
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
			), 
			'editBewerbungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editBewerbungen'],
				'href'                => 'table=tl_fernschach_turniere_bewerbungen',
				'icon'                => 'bundles/contaofernschach/images/turnier_bewerbungen.png',
				'button_callback'     => array('tl_fernschach_turniere', 'bewerbungenIcon')
			),
			'editMeldungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editMeldungen'],
				'href'                => 'table=tl_fernschach_turniere_meldungen',
				'icon'                => 'bundles/contaofernschach/images/turnier_meldungen.png',
				'button_callback'     => array('tl_fernschach_turniere', 'meldungenIcon')
			),
			'editSpieler' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editSpieler'],
				'href'                => 'table=tl_fernschach_turniere_spieler',
				'icon'                => 'bundles/contaofernschach/images/turnier_gruppe.png',
				'button_callback'     => array('tl_fernschach_turniere', 'spielerIcon')
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['toggle'],
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_fernschach_turniere', 'toggleIcon') 
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type'), 
		'default'                     => '{title_legend},title,type;{publish_legend},published',
		'category'                    => '{title_legend},title,type;{publish_legend},published',
		'tournament'                  => '{title_legend},title,type;{tournament_legend},kennziffer,registrationDate,startDate,typ,nenngeld;{meldung_legend},onlineAnmeldung,bewerbungErlaubt,spielerMax,art,artInfo;{turnierleiter_legend},turnierleiterName,turnierleiterEmail,turnierleiterUserId;{applications_legend},applications,applicationText;{publish_legend},published',
		'group'                       => '{title_legend},title,type;{tournament_legend},kennziffer;{publish_legend},published',
	), 

	// Subpalettes
	'subpalettes' => array
	(
		'protected'                   => 'groups'
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['title'],
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['type'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'explanation'             => 'fernschach_turniere_type',
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => true,
				'helpwizard'          => true,
				'submitOnChange'      => true,
				'tl_class'            => 'w50'
			),
			'options_callback'        => array('tl_fernschach_turniere', 'getTypen'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'kennziffer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['kennziffer'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'registrationDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['registrationDate'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'clr w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_turniere', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'startDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['startDate'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_turniere', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'typ' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'radio',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['typ_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'nenngeld' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['nenngeld'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>false, 'tl_class'=>'w50', 'maxlength'=>6),
			'sql'                     => "varchar(6) NOT NULL default ''"
		),
		'onlineAnmeldung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['onlineAnmeldung'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'bewerbungErlaubt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['bewerbungErlaubt'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'spielerMax' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerMax'],
			'exclude'                 => true,
			'sorting'                 => false,
			'default'                 => 0,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit', 
				'mandatory'           => false, 
				'tl_class'            => 'w50', 
				'maxlength'           => 6
			),
			'sql'                     => "int(6) unsigned NOT NULL default 0"
		),
		'art' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'radio',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art_options'],
			'eval'                    => array
			(
				'tl_class'            => 'clr w50',
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'artInfo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['artInfo'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'turnierleiterName' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterName'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'turnierleiterEmail' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterEmail'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255, 
				'rgxp'                => 'email', 
				'decodeEntities'      => true, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'turnierleiterUserId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterUserId'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_user.CONCAT(username," (",name,")")',
			'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy'),
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'chosen'              => true,
				'mandatory'           => false, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Gibt die Liste der Bewerbungen aus
		'applications' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applications'],
			'input_field_callback'    => array('tl_fernschach_turniere', 'getApplications'),
		),
		'applicationText' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false, 'rte'=>'tinyMCE', 'helpwizard'=>true),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'bewerbungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['bewerbungen'],
		),
		'zusagen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['zusagen'],
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['published'],
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
 * Class tl_fernschach_turniere
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Core
 */
class tl_fernschach_turniere extends \Backend
{

	var $bewerbungen = array(); // Nimmt die Daten aus tl_fernschach_turniere_bewerbungen auf
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');

		$objBewerbungen = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_bewerbungen WHERE published=?")
		                                          ->execute(1);
		if($objBewerbungen->numRows)
		{
			while($objBewerbungen->next())
			{
				$this->bewerbungen[$objBewerbungen->pid]['anzahl']++;
				switch($objBewerbungen->state)
				{
					case 0: // ohne Entscheidung
						$this->bewerbungen[$objBewerbungen->pid]['unklar']++;
						break;
					case 1: // Zusage
						$this->bewerbungen[$objBewerbungen->pid]['zusagen']++;
						break;
					case 2: // Absage
						$this->bewerbungen[$objBewerbungen->pid]['absagen']++;
						break;
				}
			}
		}
		//print_r($this->bewerbungen);

	}

	/**
	 * Add the breadcrumb menu
	 */
	public function addBreadcrumb()
	{

		// Knoten in Session speichern
		if (isset($_GET['node']))
		{
			$this->Session->set('tl_fernschach_turniere_node', $this->Input->get('node'));
			$this->redirect(preg_replace('/&node=[^&]*/', '', $this->Environment->request));
		}
		$cat = $this->Session->get('tl_fernschach_turniere_node');

		// Breadcrumb-Navigation erstellen
		$breadcrumb = array();
		if($cat) // Nur bei Unterkategorien
		{
			// Kategorienbaum einschränken
			$GLOBALS['TL_DCA']['tl_fernschach_turniere']['list']['sorting']['root'] = array($cat);
		
			// Infos zur aktuellen Kategorie laden
			$objActual = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_turniere WHERE published = ? AND id = ?')
			                                     ->execute(1, $cat);
			$breadcrumb[] = '<img src="bundles/contaofernschach/images/ordner_gelb.png" width="18" height="18" alt=""> ' . $objActual->title;
			
			// Navigation vervollständigen
			$pid = $objActual->pid;
			while($pid > 0)
			{
				$objTemp = \Database::getInstance()->prepare('SELECT * FROM tl_fernschach_turniere WHERE published = ? AND id = ?')
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
			$GLOBALS['TL_DCA']['tl_fernschach_turniere']['list']['sorting']['breadcrumb'] .= '
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

		// Kategorie-Icon und Icon-Attribute zuweisen
		// turnier_hauptklasse.png / turnier_hauptklasse_inaktiv.png
		$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_hauptklasse.png' : 'bundles/contaofernschach/images/turnier_hauptklasse_inaktiv.png';
		$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_hauptklasse.png" data-icon-disabled="bundles/contaofernschach/images/turnier_hauptklasse_inaktiv.png"');

		// Return the image only
		if ($blnReturnImage)
		{
			return \Image::getHtml($image, '', $imageAttribute);
		}

		// Markiere Root-Kategorien
		if($row['pid'] == '0')
		{
			// turnier_kategorie.png / turnier_kategorie_inaktiv.png
			$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_kategorie.png' : 'bundles/contaofernschach/images/turnier_kategorie_inaktiv.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_kategorie.png" data-icon-disabled="bundles/contaofernschach/images/turnier_kategorie_inaktiv.png"');
			$label = '<strong>' . $label . '</strong>';
		}

		if($row['type'] == 'tournament')
		{
			// turnier_meldungen.png / turnier_meldungen_inaktiv.png
			$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_meldungen.png' : 'bundles/contaofernschach/images/turnier_meldungen_inaktiv.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_meldungen.png" data-icon-disabled="bundles/contaofernschach/images/turnier_meldungen_inaktiv.png"');
		}

		if($row['type'] == 'group')
		{
			// turnier_gruppe.png / turnier_gruppe_inaktiv.png
			$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_gruppe.png' : 'bundles/contaofernschach/images/turnier_gruppe_inaktiv.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_gruppe.png" data-icon-disabled="bundles/contaofernschach/images/turnier_gruppe_inaktiv.png"');
		}

		// Rückgabe der Zeile
		return \Image::getHtml($image, '', $imageAttribute) . '<a href="' . \Controller::addToUrl('node='.$row['id']) . '" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']).'"> ' . $label . '</a>'; 

	}

	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (\Input::get('tid'))
		{
			$this->toggleVisibility(Contao\Input::get('tid'), (\Input::get('state') == 1), (func_num_args() <= 12 ? null : func_get_arg(12)));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->hasAccess('tl_fernschach_turniere::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.svg';
		}

		return '<a href="' . $this->addToUrl($href) . '" title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' . Contao\Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"') . '</a> ';
	}

	/**
	 * Disable/enable a user group
	 *
	 * @param integer              $intId
	 * @param boolean              $blnVisible
	 * @param Contao\DataContainer $dc
	 *
	 * @throws Contao\CoreBundle\Exception\AccessDeniedException
	 */
	public function toggleVisibility($intId, $blnVisible, \DataContainer $dc=null)
	{
		// Set the ID and action
		\Input::setGet('id', $intId);
		\Input::setGet('act', 'toggle');

		if ($dc)
		{
			$dc->id = $intId; // see #8043
		}

		// Trigger the onload_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_turniere']['config']['onload_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_turniere']['config']['onload_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}($dc);
				}
				elseif (is_callable($callback))
				{
					$callback($dc);
				}
			}
		}

		// Check the field access
		if (!$this->User->hasAccess('tl_fernschach_turniere::published', 'alexf'))
		{
			throw new \CoreBundle\Exception\AccessDeniedException('Not enough permissions to publish/unpublish page ID ' . $intId . '.');
		}

		$objRow = $this->Database->prepare("SELECT * FROM tl_fernschach_turniere WHERE id=?")
		                         ->limit(1)
		                         ->execute($intId);

		if ($objRow->numRows < 1)
		{
			throw new \CoreBundle\Exception\AccessDeniedException('Invalid page ID ' . $intId . '.');
		}

		// Set the current record
		if ($dc)
		{
			$dc->activeRecord = $objRow;
		}

		$objVersions = new \Versions('tl_fernschach_turniere', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_turniere']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_turniere']['fields']['published']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, $dc);
				}
			}
		}

		$time = time();

		// Update the database
		$this->Database->prepare("UPDATE tl_fernschach_turniere SET tstamp=$time, published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
		               ->execute($intId);

		if ($dc)
		{
			$dc->activeRecord->tstamp = $time;
			$dc->activeRecord->published = ($blnVisible ? '1' : '');
		}

		// Trigger the onsubmit_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_turniere']['config']['onsubmit_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_turniere']['config']['onsubmit_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}($dc);
				}
				elseif (is_callable($callback))
				{
					$callback($dc);
				}
			}
		}

		$objVersions->create();

	}

	public function getApplications(\DataContainer $dc)
	{

		// Link-Prefixe generieren, ab C4 ist das ein symbolischer Link zu "contao"
		if(version_compare(VERSION, '4.0', '>='))
		{
			$linkprefix = \System::getContainer()->get('router')->generate('contao_backend');
			$imageEdit = \Image::getHtml('edit.svg', 'Bewerbung des Mitglieds bearbeiten');
		}
		else
		{
			$linkprefix = 'contao/main.php';
			$imageEdit = \Image::getHtml('edit.gif', 'Bewerbung des Mitglieds bearbeiten');
		}

		$turnier_id = $dc->activeRecord->id;

		$objApplications = \Database::getInstance()->prepare("SELECT m.id AS mitglied_id, b.nachname AS nachname, b.vorname AS vorname, b.id AS bewerbung_id, b.applicationDate AS bewerbungsdatum, b.state AS status, b.promiseDate AS zusagedatum FROM tl_fernschach_turniere_bewerbungen AS b LEFT JOIN tl_fernschach_spieler AS m ON b.spielerId = m.id WHERE b.pid=?")
		                                           ->execute($turnier_id);
		$ausgabe = '<div class="long widget">'; // Wichtig damit das Auf- und Zuklappen funktioniert
		$ausgabe .= '<table class="tl_listing showColumns">';
		$ausgabe .= '<tbody><tr>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['name'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applicationDate'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['state'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['promiseDate'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist tl_right_nowrap">&nbsp;</th>';
		$ausgabe .= '</tr>';
		$oddeven = 'odd';
		while($objApplications->next())
		{
			// Farbmarkierung festlegen
			if($objApplications->status == 0) $style = '';
			elseif($objApplications->status == 1) $style = ' style="color:green"';
			else $style = ' style="color:red"';
			$oddeven = $oddeven == 'odd' ? 'even' : 'odd';
			$ausgabe .= '<tr class="'.$oddeven.'" onmouseover="Theme.hoverRow(this,1)" onmouseout="Theme.hoverRow(this,0)">';
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.$objApplications->nachname.','.$objApplications->vorname.'</td>';
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.($objApplications->bewerbungsdatum ? date('d.m.Y', $objApplications->bewerbungsdatum) : '-').'</td>';
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.($objApplications->status == 0 ? 'ohne Entscheidung' : ($objApplications->status == 1 ? 'Zusage' : 'Absage')).'</td>';
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.($objApplications->zusagedatum ? date('d.m.Y', $objApplications->zusagedatum) : '-').'</td>';
			$ausgabe .= '<td class="tl_file_list tl_right_nowrap">';
			$ausgabe .= '<a href="'.$linkprefix.'?do=fernschach-turniere&amp;table=tl_fernschach_turniere_bewerbungen&amp;act=edit&amp;id='.$objApplications->bewerbung_id.'&amp;popup=1&amp;rt='.REQUEST_TOKEN.'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>';
			$ausgabe .= '</td>';
			$ausgabe .= '</tr>';
		}
		$ausgabe .= '</tbody></table>';
		$ausgabe .= '<p style="margin: 18px 0 0 5px;">'.$objApplications->numRows.' Bewerbung(en) gefunden</p>';
		$ausgabe .= '</div>';
		return $ausgabe;

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
			unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['tournament']);
			unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['group']);
		}
		else
		{
			// 2. - x. Ebene, dann Eltern-Typ prüfen
			$objTyp = \Database::getInstance()->prepare("SELECT type FROM tl_fernschach_turniere WHERE id = ?")
			                                  ->execute($dc->activeRecord->pid);
			if($objTyp->numRows)
			{
				if($objTyp->type == 'category')
				{
					// Keine Gruppen innerhalb von Kategorien
					unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['group']);
				}
				elseif($objTyp->type == 'tournament')
				{
					// Keine Kategorien und Turnier innerhalb von Turnieren
					unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['category']);
					unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['tournament']);
				}
				elseif($objTyp->type == 'group')
				{
					// Keine Kategorien, Turniere und Gruppen innerhalb von Gruppen
					unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['category']);
					unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['tournament']);
					unset($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options']['group']);
				}
			}
		}
		return $GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options'];
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

		if ($row['type'] == 'group')
		{
			$disablePI = true;
		}

		$return = '';

		// Return the buttons
		$imagePasteAfter = Image::getHtml('pasteafter.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id']));
		$imagePasteInto = Image::getHtml('pasteinto.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][1], $row['id']));

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
	public function bewerbungenIcon($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['bewerbungErlaubt'] && $row['type'] == 'tournament')
		{
			// Bewerbungen können bearbeitet werden
			return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> '; 
		}
		else
		{
			// Keine Bearbeitung von Bewerbungen
			$icon = 'bundles/contaofernschach/images/turnier_bewerbungen_inaktiv.png';
			$title = 'Keine Bewerbungen möglich bei diesem Eintrag';
			return '<span>'.\Image::getHtml($icon, $label).'</span> ';
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

}

