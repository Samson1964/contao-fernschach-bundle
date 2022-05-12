<?php


/**
 * Tabelle tl_fernschach_turniere_meldungen
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere_meldungen'] = array
(

	// Konfiguration
	'config' => array
	(
		'label'                       => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['mainTitle'],
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ptable'                      => 'tl_fernschach_turniere',
		'sql' => array
		(
			'keys' => array
			(
				'id'                  => 'primary',
				'pid'                 => 'index',
			)
		),
	),

	// Datensätze auflisten
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'headerFields'            => array('title', 'registrationDate', 'startDate'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_fernschach_turniere_meldungen', 'listMeldungen'),
			'disableGrouping'         => true
		),
		'label' => array
		(
			'fields'                  => array('vorname'),
		),
		'global_operations' => array
		(
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_fernschach_turniere_meldungen', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Paletten
	'palettes' => array
	(
		'default'                     => '{person_legend},vorname,nachname,spielerId;{contact_legend},plz,ort,strasse,email,fax;{memberships_legend},memberId;{turnier_legend},meldungDatum,meldungNenngeld;{nenngeld_legend},nenngeldDate,nenngeldGuthaben;{info_legend:hide},infoQualifikation,bemerkungen;{publish_legend},published'
	),

	// Felder
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Hier wird die id aus tl_fernschach_spieler eingetragen
		'spielerId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['spielerId'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_fernschach_spieler.CONCAT(nachname,", ",vorname," (",memberId,")")',
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
		'vorname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['vorname'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>40, 'tl_class'=>'w50'),
			'sql'                     => "varchar(40) NOT NULL default ''"
		),
		'nachname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['nachname'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>40, 'tl_class'=>'w50'),
			'sql'                     => "varchar(40) NOT NULL default ''"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['email'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255,
				'rgxp'                => 'email',
				'decodeEntities'      => true,
				'tl_class'            => 'w50 clr'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'fax' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['fax'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 64,
				'rgxp'                => 'phone',
				'decodeEntities'      => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'plz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['plz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'ort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['ort'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'strasse' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['strasse'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'memberId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['memberId'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
				'unique'              => true,
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'meldungDatum' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['meldungDatum'],
			'exclude'                 => true,
			'default'                 => time(),
			'inputType'               => 'text',
			'flag'                    => 6,
			'eval'                    => array
			(
				'rgxp'                => 'datim',
				'datepicker'          => true,
				'tl_class'            => 'w50 wizard'
			),
			'load_callback' => array
			(
				array('tl_fernschach_turniere_meldungen', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'meldungNenngeld' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['meldungNenngeld'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 10,
				'rgxp'                => 'digit',
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'nenngeldDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['nenngeldDate'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'date',
				'mandatory'           => false,
				'doNotCopy'           => true,
				'datepicker'          => true,
				'tl_class'            => 'w50 wizard'
			),
			'load_callback' => array
			(
				array('tl_fernschach_turniere_meldungen', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'nenngeldGuthaben' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['nenngeldGuthaben'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => false,
				'tl_class'            => 'w50 m12'
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'infoQualifikation' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['infoQualifikation'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'long'),
			'sql'                     => "text NULL"
		),
		'bemerkungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['bemerkungen'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'tl_class'=>'long'),
			'sql'                     => "text NULL"
		),
		// Enthält die id des Turniers, wo der Meldende mitspielt (wird automatisch bei der Zuordnung gefüllt)
		'teilnehmer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['teilnehmer'],
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array('doNotCopy'=>false),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);

/**
 * Class tl_member_aktivicon
 */
class tl_fernschach_turniere_meldungen extends \Backend
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
	 * Ändert das Aussehen des Toggle-Buttons.
	 * @param $row
	 * @param $href
	 * @param $label
	 * @param $title
	 * @param $icon
	 * @param $attributes
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_turniere_meldungen::published', 'alexf'))
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

	/**
	 * Toggle the visibility of an element
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_turniere_meldungen::published', 'alexf'))
		{
			$this->log('Not enough permissions to show/hide record ID "'.$intId.'"', 'tl_fernschach_turniere_meldungen toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->createInitialVersion('tl_fernschach_turniere_meldungen', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_turniere_meldungen']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_turniere_meldungen']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_fernschach_turniere_meldungen SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_fernschach_turniere_meldungen', $intId);
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
	 * Generiere eine Zeile als HTML
	 * @param array
	 * @return string
	 */
	public function listMeldungen($arrRow)
	{

		$spieler = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler();

		$temp = '<div class="tl_content_left">';
		// Vor- und Nachname
		$temp .= '<b>'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
		// Zuordnung
		if($arrRow['spielerId']) 
		{
			$temp .= ' - zugeordnet: '.$spieler[$arrRow['spielerId']].'';
		}
		else $temp .= ' - nicht zugeordnet';
		// Meldedatum
		$temp .= ' - Anmeldung am: <b>'.date('d.m.Y H:i', $arrRow['meldungDatum']).'</b>';
		$temp .= '</div>';
		return $temp;

	}

}
