<?php

/**
 * Tabelle tl_fernschach_turniere_spieler
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere_spieler'] = array
(

	// Konfiguration
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ptable'                      => 'tl_fernschach_turniere',
		'onload_callback'             => array
		(
			array('tl_fernschach_turniere_spieler', 'cacheMeldung'),
		),
		'onsubmit_callback'           => array
		(
			array('tl_fernschach_turniere_spieler', 'aktualisiereMeldung'),
		),
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
			'fields'                  => array('meldungId'),
			'headerFields'            => array('title', 'registrationDate', 'startDate'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,search,limit',
			'disableGrouping'         => true,
			'child_record_callback'   => array('tl_fernschach_turniere_spieler', 'listSpieler'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['toggle'],
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Paletten
	'palettes' => array
	(
		'default'                     => '{person_legend},meldungId;{publish_legend},published'
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
		'meldungId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['meldungId'],
			'inputType'               => 'select',
			'options_callback'        => array('tl_fernschach_turniere_spieler', 'getGemeldeteSpieler'),
			'eval'                    => array
			(
				'mandatory'           => true,
				'chosen'              => true,
				'includeBlankOption'  => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => false,
				'boolean'             => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);

/**
 * Class tl_member_aktivicon
 */
class tl_fernschach_turniere_spieler extends \Backend
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
	 * Set the timestamp to 00:00:00 (see #26)
	 *
	 * @param integer $value
	 *
	 * @return integer
	 */
	public function loadDate($value)
	{
		return strtotime(date('Y-m-d', $value) . ' 00:00:00');
	}

	/**
	 * Alle Turniere einlesen
	 *
	 * @return array
	 */
	public function getGemeldeteSpieler(DataContainer $dc)
	{
		// ID des meldefähigen Turniers ermitteln
		$objTurnier = $this->Database->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
		                             ->execute($dc->activeRecord->pid);
		// Alle Meldungen dieses Turniers laden
		$objSpieler = $this->Database->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE pid = ? ORDER BY teilnehmer ASC, nachname ASC, vorname ASC")
		                             ->execute($objTurnier->pid);

		$spieler = array();
		while ($objSpieler->next())
		{
			if($objSpieler->teilnehmer)
			{
				// Meldung im Turnier als Teilnehmer bereits registriert
				if($objSpieler->id == $dc->activeRecord->meldungId)
				{
					// Nur in Auswahlliste anzeigen, wenn Spieler-ID = Meldung-ID
					$spieler[$objSpieler->id] = $objSpieler->vorname.' '.$objSpieler->nachname.' [aktuell zugeordnet]';
				}
			}
			else
			{
				// Unbearbeitete Meldung
				$spieler[$objSpieler->id] = $objSpieler->vorname.' '.$objSpieler->nachname;
			}
		}

		return $spieler;
	}

	/**
	 * Generiere eine Zeile als HTML
	 * @param array
	 * @return string
	 */
	public function listSpieler($arrRow)
	{

		$spieler = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getMeldungen();

		$temp = '<div class="tl_content_left">';
		// Vor- und Nachname
		$temp .= '<b>'.$spieler[$arrRow['meldungId']].'</b>';
		$temp .= '</div>';
		return $temp;

	}

	public function cacheMeldung(DataContainer $dc)
	{
		//log_message('Sichere alte Zuordnung','fernschach.log');
		//log_message('tl_fernschach_turniere_spieler.id = '.$this->Input->get('id'),'fernschach.log');
    	$row = $this->Database->prepare("SELECT meldungId FROM tl_fernschach_turniere_spieler WHERE id=?")
    	                      ->execute($this->Input->get('id'));

		//log_message('meldungId = '.$row->meldungId,'fernschach.log');
    	$this->Session->set('tl_fernschach_turniere_spieler.meldungId', $row->meldungId);

	}

	/**
	 * onsubmit_callback: Wird beim Abschicken eines Backend-Formulars ausgeführt.
	 * @param $dc
	 */
	public function aktualisiereMeldung(DataContainer $dc)
	{
		// Turnier-ID in der Meldung eintragen

		//log_message('Neue Zuordnung','fernschach.log');
		//log_message('meldungId aus Session = '.$this->Session->get('tl_fernschach_turniere_spieler.meldungId'),'fernschach.log');
		//log_message('meldungId aus Bearbeitung = '.$dc->activeRecord->meldungId,'fernschach.log');

    	if($this->Session->get('tl_fernschach_turniere_spieler.meldungId') !== $dc->activeRecord->meldungId)
    	{
			$this->createInitialVersion('tl_fernschach_turniere_meldungen', $this->Session->get('tl_fernschach_turniere_spieler.meldungId'));
			$set = array
			(
				'tstamp'     => time(),
				'teilnehmer' => 0,
			);
			$objInsert = $this->Database->prepare("UPDATE tl_fernschach_turniere_meldungen %s WHERE id = ?")
			                            ->set($set)
			                            ->execute($this->Session->get('tl_fernschach_turniere_spieler.meldungId'));

			$this->createNewVersion('tl_fernschach_turniere_meldungen', $this->Session->get('tl_fernschach_turniere_spieler.meldungId'));
    	}

		$this->createInitialVersion('tl_fernschach_turniere_meldungen', $dc->activeRecord->meldungId);
		$set = array
		(
			'tstamp'     => time(),
			'teilnehmer' => $dc->activeRecord->pid,
		);
		$objInsert = $this->Database->prepare("UPDATE tl_fernschach_turniere_meldungen %s WHERE id = ?")
		                            ->set($set)
		                            ->execute($dc->activeRecord->meldungId);
		$this->createNewVersion('tl_fernschach_turniere_meldungen', $dc->activeRecord->meldungId);

	}
}
