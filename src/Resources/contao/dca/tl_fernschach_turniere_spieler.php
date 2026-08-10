<?php

/**
 * Tabelle tl_fernschach_turniere_spieler
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\DC_Table;
use Contao\DataContainer;
use Contao\Database;
use Contao\Input;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

$GLOBALS['TL_DCA']['tl_fernschach_turniere_spieler'] = array
(

	// Konfiguration
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
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
				'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_spieler']['toggle'],
				'attributes'           => 'onclick="Backend.getScrollOffset()"',
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
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
			'toggle'                  => true, // Aktiviert den Contao-eigenen Schnellschalter in der Übersicht
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
class tl_fernschach_turniere_spieler extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');
	}

	/**
	 * Setzt die Uhrzeit eines Datums auf 0:00 Uhr.
	 *
	 * Contao speichert Datumsfelder als Zeitstempel. Ohne diese Umwandlung
	 * enthielte ein am Nachmittag gespeichertes Datum auch die Uhrzeit, und
	 * Vergleiche auf Tagesgrenzen gingen schief.
	 *
	 * @param int|string $value Der gespeicherte Zeitstempel; 0 oder leer bleibt unverändert
	 *
	 * @return int|string Der Zeitstempel zur Mitternacht desselben Tages
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
		$objTurnier = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
		                             ->execute($dc->activeRecord->pid);
		// Alle Meldungen dieses Turniers laden
		$objSpieler = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_meldungen WHERE pid = ? ORDER BY teilnehmer ASC, nachname ASC, vorname ASC")
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
	 *
	 * @param mixed $arrRow
	 *
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
		//Scope::logToFile('Sichere alte Zuordnung','fernschach.log');
		//Scope::logToFile('tl_fernschach_turniere_spieler.id = '.Input::get('id'),'fernschach.log');
    	$row = Database::getInstance()->prepare("SELECT meldungId FROM tl_fernschach_turniere_spieler WHERE id=?")
    	                      ->execute(Input::get('id'));

		//Scope::logToFile('meldungId = '.$row->meldungId,'fernschach.log');
    	Scope::setBackendSessionValue('tl_fernschach_turniere_spieler.meldungId', $row->meldungId);

	}

	/**
	 * onsubmit_callback: Wird beim Abschicken eines Backend-Formulars ausgeführt.
	 * @param $dc
	 */
	public function aktualisiereMeldung(DataContainer $dc)
	{
		// Turnier-ID in der Meldung eintragen

		//Scope::logToFile('Neue Zuordnung','fernschach.log');
		//Scope::logToFile('meldungId aus Session = '.Scope::getBackendSessionValue('tl_fernschach_turniere_spieler.meldungId'),'fernschach.log');
		//Scope::logToFile('meldungId aus Bearbeitung = '.$dc->activeRecord->meldungId,'fernschach.log');

    	if(Scope::getBackendSessionValue('tl_fernschach_turniere_spieler.meldungId') !== $dc->activeRecord->meldungId)
    	{
			Scope::initializeVersion('tl_fernschach_turniere_meldungen', Scope::getBackendSessionValue('tl_fernschach_turniere_spieler.meldungId'));
			$set = array
			(
				'tstamp'     => time(),
				'teilnehmer' => 0,
			);
			$objInsert = Database::getInstance()->prepare("UPDATE tl_fernschach_turniere_meldungen %s WHERE id = ?")
			                            ->set($set)
			                            ->execute(Scope::getBackendSessionValue('tl_fernschach_turniere_spieler.meldungId'));

			Scope::createVersion('tl_fernschach_turniere_meldungen', Scope::getBackendSessionValue('tl_fernschach_turniere_spieler.meldungId'));
    	}

		Scope::initializeVersion('tl_fernschach_turniere_meldungen', $dc->activeRecord->meldungId);
		$set = array
		(
			'tstamp'     => time(),
			'teilnehmer' => $dc->activeRecord->pid,
		);
		$objInsert = Database::getInstance()->prepare("UPDATE tl_fernschach_turniere_meldungen %s WHERE id = ?")
		                            ->set($set)
		                            ->execute($dc->activeRecord->meldungId);
		Scope::createVersion('tl_fernschach_turniere_meldungen', $dc->activeRecord->meldungId);

	}
}
