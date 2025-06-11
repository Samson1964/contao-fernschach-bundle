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
		'onload_callback' => array
		(
			array('tl_fernschach_turniere_meldungen', 'checkPermission')
		),
		'onsubmit_callback'           => array
		(
			array('tl_fernschach_turniere_meldungen', 'setSpielername'),
			array('tl_fernschach_turniere_meldungen', 'AktualisiereBuchungen')
		),
		'ondelete_callback'           => array
		(
			array('tl_fernschach_turniere_meldungen', 'InfoTurnierleiter'),
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
			'fields'                  => array('meldungDatum DESC'),
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
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'].' '.@$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
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
		'__selector__'                => array('player'),
		'default'                     => '{hinweis_legend},nenngeldInfo;{meldedatum_legend},meldungDatum;{person_legend},vorname,nachname;{memberships_legend},spielerId,memberId;{contact_legend:hide},plz,ort,strasse,email,fax;{turnier_legend};{nenngeld_legend},meldungNenngeld,nenngeldDate,nenngeldGuthaben;{info_legend:hide},infoQualifikation,bemerkungen;{player_legend},player;{publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'player'                      => 'playerIn'
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
		'player' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['player'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true,
				'submitOnChange'      => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'playerIn' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_meldungen']['playerIn'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_fernschach_turniere_meldungen', 'getTurniere'),
			'eval'                    => array
			(
				'tl_class'            => 'long',
				'includeBlankOption'  => true,
				'chosen'              => true
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
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
	 * Check permissions to edit table tl_news
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
		if(!$this->User->hasAccess('create', 'fernschach_turniere_meldungen')) $GLOBALS['TL_DCA']['tl_fernschach_turniere_meldungen']['config']['closed'] = true;

		// Zugriff auf lokale Operationen prüfen
		if(!$this->User->hasAccess('delete', 'fernschach_turniere_meldungen')) unset($GLOBALS['TL_DCA']['tl_fernschach_turniere_meldungen']['list']['operations']['delete']);

		// Aktuelle Aktion von act prüfen
		switch(Input::get('act'))
		{
			case 'create': // Turnieranmeldung anlegen
				if(!$this->User->hasAccess('create', 'fernschach_turniere_meldungen'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine neue Turnieranmeldung anzulegen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
			case 'delete': // Turnieranmeldung löschen
				if(!$this->User->hasAccess('delete', 'fernschach_turniere_meldungen'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Turnieranmeldung zu löschen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}

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
		$temp .= '<span style="display:inline-block; width:290px;">';
		$temp .= '<span title="Anmeldedatum"><b>'.date('d.m.Y H:i', $arrRow['meldungDatum']).'</b> | </span>';

		// Vor- und Nachname
		$temp .= ' <span title="Anmeldename">';
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
		$temp .= '</span>';

		// Verlinkung des zugeordneten Spielers vorbereiten
		$linkprefix = \System::getContainer()->get('router')->generate('contao_backend');

		// Zuordnung Mitglied
		$temp .= '<span style="display:inline-block; width:290px;" title="Mitglied">Mitglied:';
		if($arrRow['spielerId'])
		{
			$temp .= ' <a style="color:blue;" href="'.$linkprefix.'?do=fernschach-spieler&amp;act=edit&amp;id='.$arrRow['spielerId'].'&amp;popup=1&amp;rt='.REQUEST_TOKEN.' " onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Spieler bearbeiten\',\'url\':this.href});return false">'.$spieler[$arrRow['spielerId']]['vorname'].' '.$spieler[$arrRow['spielerId']]['nachname'].'</a>';
			if($spieler[$arrRow['spielerId']]['sepaNenngeld'])
			{
				$temp .= ' (SEPA <img src="bundles/contaofernschach/images/ja.png" width="12">)';
			}
			else
			{
				$temp .= ' (SEPA <img src="bundles/contaofernschach/images/nein.png" width="12">)';
			}
		}
		else $temp .= ' -';
		$temp .= '</span>';

		// Zuordnung Turnierteilnehmer
		$temp .= '<span style="display:inline-block;" title="Teilnehmer">Teilnehmer: ';
		if($arrRow['player'])
		{
			if($arrRow['playerIn'])
			{
				$temp .= '<img src="bundles/contaofernschach/images/ja.png" width="12">';
				$objTurnier = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id=?")
				                                      ->execute($arrRow['playerIn']);

				if($objTurnier->numRows)
				{
					$temp .= ' '.$objTurnier->title;
				}
			}
			else
			{
				$temp .= '<img src="bundles/contaofernschach/images/ja.png" width="12">';
			}
		}
		else
		{
			$temp .= '<img src="bundles/contaofernschach/images/nein.png" width="12">';
		}
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
		$result = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld WHERE meldungId = ? AND typ = ?")
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
			$objInsert = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto_nenngeld %s WHERE id = ?")
			                                     ->set($set)
			                                     ->execute($result->id);
			$this->createNewVersion('tl_fernschach_spieler_konto_nenngeld', $result->id);
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
			$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto_nenngeld %s")
			                                     ->set($set)
			                                     ->execute();
		}

		// ************************************************************
		// Suche nach Habenbuchung für diese Meldung
		// ************************************************************
		$result = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler_konto_nenngeld WHERE meldungId = ? AND typ = ?")
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
				$objInsert = \Database::getInstance()->prepare("UPDATE tl_fernschach_spieler_konto_nenngeld %s WHERE id = ?")
				                                     ->set($set)
				                                     ->execute($result->id);
				$this->createNewVersion('tl_fernschach_spieler_konto_nenngeld', $result->id);
			}
			// Buchung löschen, da kein Überweisungsdatum gesetzt ist
			else
			{
				$answer = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE id = ?")
				                                  ->execute($result->id);
				$this->createNewVersion('tl_fernschach_spieler_konto_nenngeld', $result->id);
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
				$objInsert = \Database::getInstance()->prepare("INSERT INTO tl_fernschach_spieler_konto_nenngeld %s")
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
		$this->import(\BackendUser::class, 'User');

		// Löscht alle Buchungen zu dieser Meldung
		$result = \Database::getInstance()->prepare("DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE meldungId = ?")
		                                  ->execute($dc->activeRecord->id);

		return;

		// Siehe DC_Table.php Funktion delete -> muß noch ausgebaut werden!
		$set = array
		(
			'pid'          => $this->User->id, 
			'tstamp'       => time(), 
			'fromTable'    => 'tl_fernschach_spieler_konto_nenngeld', 
			'query'        => 'DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE meldungId='.$dc->activeRecord->id, 
			'affectedRows' => $affected, 
			'data'         => serialize($data)
		);

		$undoset = \Database::getInstance()->prepare("INSERT INTO tl_undo %s")
		                                   ->set($set)
		                                   ->execute();

	}

	/**
	 * ondelete_callback: Wird ausgeführt bevor ein Datensatz aus der Datenbank entfernt wird.
	 * @param $dc
	 */
	public function InfoTurnierleiter(\DataContainer $dc)
	{
		// E-Mail für Turnierleiter zusammenbauen, da eine Anmeldung gelöscht wird
		$turnierleiter = \Schachbulle\ContaoFernschachBundle\Classes\Turnier::getTurnierleiter($dc->activeRecord->pid);
		// Turnier laden
		$turnier = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getTurnierdatensatz($dc->activeRecord->pid);

		if(isset($turnierleiter[0]))
		{
			// Email verschicken
			$objEmail = new \Email();
			$objEmail->charset = 'utf-8';
			$objEmail->from = $GLOBALS['TL_CONFIG']['fernschach_emailAdresse'];
			$objEmail->fromName = $GLOBALS['TL_CONFIG']['fernschach_emailVon'];
			$objEmail->sendBcc($GLOBALS['TL_CONFIG']['fernschach_emailVon'].' <'.$GLOBALS['TL_CONFIG']['fernschach_emailAdresse'].'>');
			$objEmail->subject = 'Turnieranmeldung von '.$dc->activeRecord->vorname.' '.$dc->activeRecord->nachname.' gelöscht';
			$objEmail->replyTo($turnierleiter[0]['name'].' <'.$turnierleiter[0]['email'].'>');
			// Weitere Empfänger einbauen
			if(count($turnierleiter) > 1)
			{
				$empfaenger = array();
				for($x = 1; $x < count($turnierleiter); $x++)
				{
					if($turnierleiter[$x]['email']) $empfaenger[] = $turnierleiter[$x]['name'] . ' <' . $turnierleiter[$x]['email'] . '>';
				}
				$cc = implode(',', $empfaenger);
				$objEmail->sendCc($cc);
			}
			// Backend-Link zum Turnier generieren
			$backendlink = $this->replaceInsertTags('{{env::url}}').'/contao?do=undo';
			// Kommentar zusammenbauen
			$text = '<html><head><title></title></head><body>';
			$text .= '<p>Eine Turnieranmeldung wurde gelöscht.</p>';
			$text .= '<h2>Gelöschte Anmeldung:</h2>';
			$text .= '<ul>';
			$text .= '<li>Nachname: <b>'.$dc->activeRecord->nachname.'</b></li>';
			$text .= '<li>Vorname: <b>'.$dc->activeRecord->vorname.'</b></li>';
			$text .= '<li>Datum der Meldung: <b>'.date('d.m.Y H:i', $dc->activeRecord->meldungDatum).'</b></li>';
			$text .= '</ul>';
			$text .= '<h2>War gemeldet für das Turnier:</h2>';
			$text .= '<ul>';
			$text .= '<li>Titel: <b>'.$turnier->title.'</b></li>';
			$text .= '</ul>';
			$text .= '<p>Der Datensatz mit der Turnieranmeldung kann im Backend wiederhergestellt werden: System -> <a href="'.$backendlink.'">Wiederherstellen</a><br>';
			$text .= '<b>Der korrelierende Datensatz mit der Nenngeld-Buchung kann nicht wiederhergestellt werden!</b> (Das soll in einer späteren Version möglich werden.)</p>';
			$text .= '<p><i>Diese E-Mail wurde automatisch erstellt.</i></p></body></html>';

			// Add the comment details
			$objEmail->html = $text;
			$objEmail->sendTo(array($turnierleiter[0]['name'].' <'.$turnierleiter[0]['email'].'>'));
		}
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

	/**
	 * options_callback: Ermöglicht das Befüllen eines Drop-Down-Menüs oder einer Checkbox-Liste mittels einer individuellen Funktion.
	 * @param  $dc
	 * @return array
	 */
	public function getTurniere(\DataContainer $dc)
	{
		$arr = array();
		$act = \Input::get('act');

		if($act)
		{
			switch($act)
			{
				case 'edit': // Bearbeitungsformular der Meldung  
					// Nach Turnieren suchen, deren übergeordnetes Turnier gleich der pid ist
					$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE pid=? ORDER BY title ASC")
					                                       ->execute($dc->activeRecord->pid);
					break;
				case 'editAll': // Mehrere bearbeiten
				case 'overrideAll': // Mehrere überschreiben
					// Nach Turnieren suchen, deren übergeordnetes Turnier gleich der pid ist
					$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE pid=? ORDER BY title ASC")
					                                       ->execute(\Input::get('id'));
					break;
				default:
			}
		}
		
		if(isset($objTurniere) && $objTurniere->numRows)
		{
			while($objTurniere->next())
			{
				$arr[$objTurniere->id] = $objTurniere->title;
			}
		}
		return $arr;
	}

}
