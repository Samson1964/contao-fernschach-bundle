<?php

/**
 * Tabelle tl_fernschach_turniere
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere'] = array
(

	// Konfiguration
	'config' => array
	(
		'dataContainer'               => 'Table',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		),
	),

	// Datensätze auflisten
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('startDate'),
			'flag'                    => 11,
			'panelLayout'             => 'filter;sort,search,limit',
		),
		'label' => array
		(
			// Das Feld aktiv wird vom label_callback überschrieben
			'fields'                  => array('titel', 'startDate', 'bewerbungen', 'zusagen'),
			'showColumns'             => true,
			'format'                  => '%s',
			'label_callback'          => array('tl_fernschach_turniere', 'viewLabels'),
		),
		'global_operations' => array
		(
			'import' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['import'],
				'href'                => 'key=importTurniere',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_fernschach_turniere', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Paletten
	'palettes' => array
	(
		'default'                     => '{tournament_legend},titel,kennziffer,registrationDate,startDate,art,nenngeld;;{turnierleiter_legend},turnierleiterName,turnierleiterEmail;{applications_legend},applications;{publish_legend},published'
	),

	// Felder
	'fields' => array
	(
		'id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['id'],
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['tstamp'],
			'sorting'                 => true,
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'titel' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['titel'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => true, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
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
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
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
		'art' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'radio',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art_options'],
			'eval'                    => array('tl_class'=>'w50', 'includeBlankOption'=>false),
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
		// Gibt die Liste der Bewerbungen aus
		'applications' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applications'],
			'input_field_callback'    => array('tl_fernschach_turniere', 'getApplications'),
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
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
	)
);

/**
 * Class tl_member_aktivicon
 */
class tl_fernschach_turniere extends Backend
{

	var $bewerbungen =array();
	var $status =array();
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');

		// Bewerbungen und Zusagen aller Turniere zählen
		$objRecord = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turnierbewerbungen")
		                                     ->execute();
		if($objRecord->numRows)
		{
			while($objRecord->next())
			{
				if($objRecord->applicationDate) $this->bewerbungen[$objRecord->tournament]++; 
				$this->status[$objRecord->tournament][$objRecord->state]++; 
			}
		}

	}

	public function getApplications(DataContainer $dc)
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

		$objApplications = $this->Database->prepare("SELECT m.id AS mitglied_id, m.nachname AS nachname, m.vorname AS vorname, a.id AS bewerbung_id, a.applicationDate AS bewerbungsdatum, a.state AS status, a.promiseDate AS zusagedatum FROM tl_fernschach_turnierbewerbungen AS a LEFT JOIN tl_fernschach_spieler AS m ON a.pid = m.id WHERE a.tournament=?")
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
			$ausgabe .= '<a href="'.$linkprefix.'?do=fernschach-spieler&amp;table=tl_fernschach_turnierbewerbungen&amp;act=edit&amp;id='.$objApplications->bewerbung_id.'&amp;popup=1&amp;rt='.REQUEST_TOKEN.'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>';
			$ausgabe .= '</td>';
			$ausgabe .= '</tr>';
		}
		$ausgabe .= '</tbody></table>';
		$ausgabe .= '</div>';
		return $ausgabe;

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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_turniere::published', 'alexf'))
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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_turniere::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_fernschach_turniere toggleVisibility', TL_ERROR);
			// Zurücklink generieren, ab C4 ist das ein symbolischer Link zu "contao"
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
		
		$this->createInitialVersion('tl_fernschach_turniere', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_turniere']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_turniere']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_fernschach_turniere SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_fernschach_turniere', $intId);
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
	 * Zeigt zu einem Datensatz die Anzahl der Bewerbungen/Zusagen an
	 *
	 * @param array                $row
	 * @param string               $label
	 * @param Contao\DataContainer $dc
	 * @param array                $args        Index 6 ist das Feld lizenzen
	 *
	 * @return array
	 */
	public function viewLabels($row, $label, Contao\DataContainer $dc, $args)
	{

		$args[2] = $this->bewerbungen[$row['id']];
		$args[3] = '';
		if($this->status[$row['id']][0]) $args[3] .= '<span title="Anzahl der nicht geklärten Bewerbungen">'.$this->status[$row['id']][0].$this->generateImage('bundles/contaofernschach/images/fragezeichen.png', 'ohne Entscheidung').'</span>&nbsp;';
		if($this->status[$row['id']][1]) $args[3] .= '<span title="Anzahl der Zusagen">'.$this->status[$row['id']][1].$this->generateImage('bundles/contaofernschach/images/ja.png', 'Zusagen').'</span>&nbsp;';
		if($this->status[$row['id']][2]) $args[3] .= '<span title="Anzahl der Absagen">'.$this->status[$row['id']][2].$this->generateImage('bundles/contaofernschach/images/nein.png', 'Absagen').'</span>';

		// Datensatz komplett zurückgeben
		return $args;
	}

}
