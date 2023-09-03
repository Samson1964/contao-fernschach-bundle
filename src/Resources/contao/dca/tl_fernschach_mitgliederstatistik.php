<?php

/**
 * Tabelle tl_fernschach_mitgliederstatistik
 */
$GLOBALS['TL_DCA']['tl_fernschach_mitgliederstatistik'] = array
(

	// Konfiguration
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'sql'                         => array
		(
			'keys'                    => array
			(
				'id'                    => 'primary',
			)
		),
	),

	// Datensätze auflisten
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title','ageperiods'),
			'flag'                    => 12,
			'panelLayout'             => 'filter;sort,search,limit',
			'disableGrouping'         => true,
		),
		'label' => array
		(
			// Das Feld aktiv wird vom label_callback überschrieben
			'fields'                  => array('title','ageperiods'),
			'showColumns'             => true,
			'label_callback'          => array('tl_fernschach_mitgliederstatistik', 'convertAgeperiods')
		),
		'global_operations' => array
		(
			'statistik' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['statistik'],
				'href'                => 'key=statistik',
				'icon'                => 'bundles/contaofernschach/images/statistik.png'
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['toggle'],
				'attributes'           => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'            => 'published',
					'options'          => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Paletten
	'palettes' => array
	(
		'default'                     => '{title_legend},title;{ageperiods_legend},ageperiods;{publish_legend},published'
	),

	// Felder
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['tstamp'],
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['title'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => true, 
				'maxlength'           => 255, 
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'ageperiods' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['ageperiods'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'tl_class'            => 'clr',
				'buttonPos'           => 'top',
				'columnFields'        => array
				(
					'from' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['ageperiods_from'],
						'exclude'                 => true,
						'search'                  => false,
						'sorting'                 => true,
						'flag'                    => 12,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 10,
							'style'               => 'width: 200px',
							'rgxp'                => 'alnum'
						),
					),
					'to' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['ageperiods_to'],
						'exclude'                 => true,
						'search'                  => false,
						'sorting'                 => true,
						'flag'                    => 12,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 10,
							'style'               => 'width: 200px',
							'rgxp'                => 'alnum'
						),
					),
				)
			),
			'sql'                   => "blob NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
				'doNotCopy'           => false
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);

/**
 * Class tl_member_aktivicon
 */
/**
 * Class tl_member_aktivicon
 */
class tl_fernschach_mitgliederstatistik extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function convertAgeperiods($row, $label, DataContainer $dc, $args) 
	{ 
		// Altersstruktur für Anzeige aufbereiten
		$struktur = unserialize($row['ageperiods']);
		$temp = '';
		if(is_array($struktur))
		{
			foreach($struktur as $item)
			{
				$temp .= $item['from'].'-'.$item['to'].', ';
			}
			$args[1] = substr($temp, 0, -2);
		}
		else
		{
			$args[1] = 'kein Array';
		}
		return $args; 
	}  
}
