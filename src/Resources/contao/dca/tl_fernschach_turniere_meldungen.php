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
		'onsubmit_callback'           => array
		(
			array('tl_fernschach_turniere_meldungen', 'setSpielername'),
			array('tl_fernschach_turniere_meldungen', 'AktualisiereBuchungen')
		),
		'ondelete_callback'           => array
		(
			array('tl_fernschach_turniere_meldungen', 'LoescheBuchungen')
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
			'headerFields'            => array('title', 'nenngeld', 'registrationDate', 'startDate'),
			'flag'                    => 12,
			'fields'                  => array('tstamp DESC'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_fernschach_turniere_meldungen', 'listMeldungen'),
			'disableGrouping'         => true
		),
		'label' => array
		(
			'fields'                  => array('nachname', 'vorname'),
			'showColumns'             => true,
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
				'attributes'          => 'style="margin-right:3px"'
			),
			'tournaments' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['tournaments'],
				'href'                => 'do=fernschach-turniere-spieler',
				'icon'                => 'bundles/contaofernschach/images/tournament.png',
			),
		)
	),

	// Paletten
	'palettes' => array
	(
		'default'                     => '{hinweis_legend},nenngeldInfo;{meldedatum_legend},meldungDatum;{person_legend},vorname,nachname;{memberships_legend},spielerId,memberId;{contact_legend:hide},plz,ort,strasse,email,fax;{turnier_legend};{nenngeld_legend},meldungNenngeld,nenngeldDate,nenngeldGuthaben;{info_legend:hide},infoQualifikation,bemerkungen;{publish_legend},published'
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
			'foreignKey'              => 'tl_fernschach_turniere.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['tstamp'],
			'flag'                    => 5,
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
				'submitOnChange'      => true,
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
			//'load_callback' => array
			//(
			//	array('tl_fernschach_turniere_meldungen', 'loadDate')
			//),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'nenngeldInfo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['nenngeldInfo'],
			'input_field_callback'    => array('tl_fernschach_turniere_meldungen', 'getInfo'),
			'exclude'                 => false,
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

		// Meldedatum
		$temp .= '<span style="display:inline-block; width:110px;" title="Anmeldedatum"><b>'.date('d.m.Y H:i', $arrRow['meldungDatum']).'</b></span>';

		// Vor- und Nachname
		$temp .= '<span style="display:inline-block; width:200px;" title="Anmeldename">';
		if(isset($arrRow['state']))
		{
			if($arrRow['state'] == 0) $temp .= '<b>'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
			elseif($arrRow['state'] == 1) $temp .= '<b style="color:green">'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
			else $temp .= '<b style="color:red">'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
		}
		else
		{
			$temp .= '<b>'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
		}
		$temp .= '</span>';
		
		// Zuordnung
		$temp .= '<span style="display:inline-block;" title="Zugeordnet">Zugeordnet:';
		if($arrRow['spielerId'])
		{
			$temp .= ' '.$spieler[$arrRow['spielerId']]['vorname'].' '.$spieler[$arrRow['spielerId']]['nachname'];
			if($spieler[$arrRow['spielerId']]['sepaNenngeld'])
			{
				$temp .= ', (SEPA-Mandat: Ja)';
			}
			else
			{
				$temp .= ', (SEPA-Mandat: Nein)';
			}
		}
		else $temp .= ' -';
		$temp .= '</span>';

		$temp .= '</div>';
		return $temp;

	}

	public function getInfo(\DataContainer $dc)
	{

		$string = '
<div class="long clr widget">
	<h3><label>'.$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['nenngeldInfo'][0].'</label></h3><span style="color:green;">
	'.$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['nenngeldInfo'][1].'</span>
</div>';

		return $string;
	}

	public function AktualisiereBuchungen(\DataContainer $dc)
	{
		//log_message('dc->activeRecord:','fernschach.log');
		//log_message(print_r($dc->activeRecord,true),'fernschach.log');

		// ************************************************************
		// Turnier laden
		// ************************************************************
		$turnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($dc->activeRecord->pid);

		// ************************************************************
		// ID des zugeordneten Spielers ermitteln
		// ************************************************************
		$pid = 0;
		if($dc->activeRecord->spielerId)
		{
			// Spieler zugeordnet, das hat Priorität
			$pid = $dc->activeRecord->spielerId;
		}
		elseif($dc->activeRecord->memberId)
		{
			// Kein Spieler zugeordnet, aber Mitgliedsnummer vorhanden - dann Spieler anhand Mitgliedsnummer suchen
			$spieler = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpielerdatensatz(NULL, $dc->activeRecord->memberId);
			if($spieler->numRows()) $pid = $spieler->memberId;
		}
		if(!$pid) return; // Nichts gefunden, keine Buchungen anlegen/aktualisieren

		// ************************************************************
		// Suche nach Sollbuchung für diese Meldung
		// ************************************************************
		$result = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE meldungId = ? AND typ = ?")
		                                  ->execute($dc->activeRecord->id, 's');
		if($result->numRows)
		{
			// Buchung gefunden -> aktualisieren
			$set = array
			(
				'tstamp'           => time(),
				'betrag'           => $turnier->nenngeld,
				'datum'            => $dc->activeRecord->meldungDatum,
				'art'              => 'n', // nation. Turnier
				'verwendungszweck' => 'Nenngeld-Forderung',
				'turnier'          => $turnier->id,
				'comment'          => $result->comment .= "\nDatensatz automatisch aktualisiert am ".date('d.m.Y H:i'),
			);
			//log_message('set (UPDATE):','fernschach.log');
			//log_message(print_r($set,true),'fernschach.log');
			$objInsert = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
			                                     ->set($set)
			                                     ->execute($result->id);
			$this->createNewVersion('tl_fernschach_spieler_konto', $result->id);
		}
		else
		{
			// Buchung nicht gefunden -> anlegen
			$set = array
			(
				'pid'              => $pid,
				'tstamp'           => time(),
				'meldungId'        => $dc->activeRecord->id,
				'betrag'           => $turnier->nenngeld,
				'typ'              => 's',
				'datum'            => $dc->activeRecord->meldungDatum,
				'kategorie'        => $dc->activeRecord->nenngeldGuthaben ? 'g' : '', // Guthabenbuchung oder nichts eintragen
				'art'              => 'n', // nation. Turnier
				'verwendungszweck' => 'Nenngeld-Forderung'.($dc->activeRecord->nenngeldGuthaben ? ' (Verrechnung mit Guthaben gewünscht)' : ''),
				'turnier'          => $turnier->id,
				'comment'          => 'Datensatz automatisch erzeugt am '.date('d.m.Y H:i'),
				'published'        => 1,
			);
			//log_message('set (INSERT):','fernschach.log');
			//log_message(print_r($set,true),'fernschach.log');
			$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto %s")
			                                     ->set($set)
			                                     ->execute();
		}

		// ************************************************************
		// Suche nach Habenbuchung für diese Meldung
		// ************************************************************
		$result = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto WHERE meldungId = ? AND typ = ?")
		                                  ->execute($dc->activeRecord->id, 'h');
		if($result->numRows)
		{
			// Buchung gefunden -> aktualisieren, wenn Überweisungsdatum gesetzt ist
			if($dc->activeRecord->nenngeldDate)
			{
				$set = array
				(
					'tstamp'           => time(),
					'kategorie'        => $dc->activeRecord->nenngeldGuthaben ? 'g' : '', // Guthabenbuchung oder nichts eintragen
					'betrag'           => $dc->activeRecord->meldungNenngeld,
					'datum'            => $dc->activeRecord->nenngeldDate,
					'art'              => 'n', // nation. Turnier
					'turnier'          => $turnier->id,
					'comment'          => $result->comment .= "\nDatensatz automatisch aktualisiert am ".date('d.m.Y H:i'),
				);
				//log_message('set (UPDATE):','fernschach.log');
				//log_message(print_r($set,true),'fernschach.log');
				$objInsert = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto %s WHERE id = ?")
				                                     ->set($set)
				                                     ->execute($result->id);
				$this->createNewVersion('tl_fernschach_spieler_konto', $result->id);
			}
			// Buchung löschen, da kein Überweisungsdatum gesetzt ist
			else
			{
				$answer = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE id = ?")
				                                  ->execute($result->id);
				$this->createNewVersion('tl_fernschach_spieler_konto', $result->id);
			}
		}
		else
		{
			// Buchung nicht gefunden -> anlegen, wenn Überweisungsdatum ausgefüllt ist
			if($dc->activeRecord->nenngeldDate)
			{
				$set = array
				(
					'pid'              => $pid,
					'tstamp'           => time(),
					'meldungId'        => $dc->activeRecord->id,
					'typ'              => 'h',
					'kategorie'        => $dc->activeRecord->nenngeldGuthaben ? 'g' : '', // Guthabenbuchung oder nichts eintragen
					'betrag'           => $dc->activeRecord->meldungNenngeld,
					'datum'            => $dc->activeRecord->nenngeldDate,
					'art'              => 'n', // nation. Turnier
					'verwendungszweck' => 'Nenngeld-Zahlung',
					'turnier'          => $turnier->id,
					'comment'          => 'Datensatz automatisch erzeugt am '.date('d.m.Y H:i'),
					'published'        => 1,
				);
				//log_message('set (INSERT):','fernschach.log');
				//log_message(print_r($set,true),'fernschach.log');
				$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto %s")
				                                     ->set($set)
				                                     ->execute();
			}
		}

		return;
	}

	/**
	 * ondelete_callback: Wird ausgeführt bevor ein Datensatz aus der Datenbank entfernt wird.
	 * @param $dc
	 */
	public function LoescheBuchungen(\DataContainer $dc)
	{
		// Löscht alle Buchungen zu dieser Meldung
		$result = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto WHERE meldungId = ?")
		                                  ->execute($dc->activeRecord->id);
	}

	/**
	 * Setzt die Felder Vorname und Nachname, wenn diese nicht gefüllt sind
	 * @param mixed
	 * @param \DataContainer
	 * @return string
	 * @throws \Exception
	 */
	public function setSpielername(\DataContainer $dc)
	{
		$nachname = $dc->activeRecord->nachname;
		$vorname = $dc->activeRecord->vorname;

		if(!$nachname && $dc->activeRecord->spielerId)
		{
			// Kein Nachname, dann Nachname aus Spielertabelle holen
			$nachname = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler($dc->activeRecord->spielerId, 'nachname');
			\Database::getInstance()->prepare("UPDATE tl_fernschach_turniere_meldungen SET nachname = ? WHERE id = ?")
			                        ->execute($nachname, $dc->id);
			$this->createNewVersion('tl_fernschach_turniere_meldungen', $dc->id);
		}

		if(!$vorname && $dc->activeRecord->spielerId)
		{
			// Kein Vorname, dann Vorname aus Spielertabelle holen
			$vorname = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler($dc->activeRecord->spielerId, 'vorname');
			\Database::getInstance()->prepare("UPDATE tl_fernschach_turniere_meldungen SET vorname = ? WHERE id = ?")
			                        ->execute($vorname, $dc->id);
			$this->createNewVersion('tl_fernschach_turniere_meldungen', $dc->id);
		}

	}

}
