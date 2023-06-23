<?php

/**
 * Tabelle tl_fernschach_spieler
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler'] = array
(

	// Konfiguration
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ctable'                      => array('tl_fernschach_spieler_konto'),
		'onload_callback'             => array
		(
			array('tl_fernschach_spieler', 'checkPermission'),
			array('tl_fernschach_spieler', 'applyAdvancedFilter'),
			array('\Schachbulle\ContaoFernschachBundle\Classes\Helper', 'updateResetbuchungen'),
			array('\Schachbulle\ContaoFernschachBundle\Classes\Maintenance', 'getMaintenance'),
		),
		'sql'                         => array
		(
			'keys'                    => array
			(
				'id'                    => 'primary',
				'memberId'              => 'index',
				'memberInternationalId' => 'index',
				'nachname'              => 'index'
			)
		),
	),

	// Datensätze auflisten
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('nachname','vorname','memberId'),
			'flag'                    => 1,
			'panelLayout'             => 'myfilter;filter;sort,search,limit',
			'panel_callback'          => array('myfilter' => array('tl_fernschach_spieler', 'generateAdvancedFilter')),
		),
		'label' => array
		(
			'fields'                  => array('memberId','nachname','vorname','birthday','plz','ort','saldo','accountChecked'),
			'showColumns'             => true,
			'format'                  => '%s',
			'label_callback'          => array('tl_fernschach_spieler', 'listMembers')
		),
		'global_operations' => array
		(
			'importSpieler' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['importSpieler'],
				'href'                => 'key=importSpieler',
				'icon'                => 'bundles/contaofernschach/images/import.png',
			),
			'exportXLS' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['exportXLS'],
				'href'                => 'key=exportXLS',
				'icon'                => 'bundles/contaofernschach/images/exportEXCEL.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
				'button_callback'     => array('tl_fernschach_spieler', 'generateEditButton')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
				'button_callback'     => array('tl_fernschach_spieler', 'generateCopyButton')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_fernschach_spieler', 'generateDeleteButton')
			),
			'konto' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['konto'],
				'href'                => 'table=tl_fernschach_spieler_konto',
				'icon'                => 'bundles/contaofernschach/images/euro.png',
				'button_callback'     => array('tl_fernschach_spieler', 'generateKontoButton')
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['toggle'],
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
				//'button_callback'     => array('tl_fernschach_spieler', 'generateToggleButton')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['show'],
				'href'                => 'act=show',
				'icon'                => 'bundles/contaofernschach/images/show.svg',
				'attributes'          => 'style="margin-right:3px"',
				'button_callback'     => array('tl_fernschach_spieler', 'generateShowButton')
			),
			'fertigIcon' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fertigIcon'],
				'attributes'          => 'onclick="Backend.getScrollOffset();"',
				'haste_ajax_operation' => array
				(
					'field'           => 'fertig',
					'options'         => array
					(
						array('value' => '', 'icon' => 'bundles/contaofernschach/images/unfertig.png'),
						array('value' => '1', 'icon' => 'bundles/contaofernschach/images/fertig.png'),
					)
				)
			),
		)
	),

	// Paletten
	'palettes' => array
	(
		'__selector__'                => array('death', 'fgm_title', 'sim_title', 'fim_title', 'ccm_title', 'lgm_title', 'cce_title', 'lim_title', 'gm_title', 'im_title', 'wgm_title', 'fm_title', 'wim_title', 'cm_title', 'wfm_title', 'wcm_title', 'honor_25', 'honor_40', 'honor_50', 'honor_60', 'honor_70', 'honor_president', 'honor_member'),
		'default'                     => '{archived_legend:hide},archived;{assign_legend:hide},memberAssign;{person_legend},nachname,vorname,titel,anrede,briefanrede,status;{live_legend},birthday,birthplace,sex,death;{adresse_legend:hide},plz,ort,strasse,adresszusatz;{adresse2_legend:hide},plz2,ort2,strasse2,adresszusatz2;{telefon_legend:hide},telefon1,telefon2;{telefax_legend:hide},telefax1,telefax2;{email_legend:hide},email1,email2;{memberships_legend},memberId,memberInternationalId,streichung,memberships,verein;{alternativ_legend:hide},gastNummer,servertesterNummer,fremdspielerNummer;{zuzug_legend:hide},zuzug;{turnier_legend:hide},klassenberechtigung;{iccf_legend:hide},fgm_title,sim_title,fim_title,ccm_title,lgm_title,cce_title,lim_title,titelinfo;{fide_legend:hide},gm_title,im_title,wgm_title,fm_title,wim_title,cm_title,wfm_title,wcm_title;{normen_legend},normen;{honors_legend},honor_25,honor_40,honor_50,honor_60,honor_70,honor_president,honor_member;{bank_legend:hide},inhaber,iban,bic;{info_legend:hide},info;{publish_legend},published,fertig'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'death'                       => 'deathday,deathplace',
		'fgm_title'                   => 'fgm_date',
		'sim_title'                   => 'sim_date',
		'fim_title'                   => 'fim_date',
		'ccm_title'                   => 'ccm_date',
		'lgm_title'                   => 'lgm_date',
		'cce_title'                   => 'cce_date',
		'lim_title'                   => 'lim_date',
		'gm_title'                    => 'gm_date',
		'im_title'                    => 'im_date',
		'wgm_title'                   => 'wgm_date',
		'fm_title'                    => 'fm_date',
		'wim_title'                   => 'wim_date',
		'cm_title'                    => 'cm_date',
		'wfm_title'                   => 'wfm_date',
		'wcm_title'                   => 'wcm_date',
		'honor_25'                    => 'honor_25_date',
		'honor_40'                    => 'honor_40_date',
		'honor_50'                    => 'honor_50_date',
		'honor_60'                    => 'honor_60_date',
		'honor_70'                    => 'honor_70_date',
		'honor_president'             => 'honor_president_date',
		'honor_member'                => 'honor_member_date',
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['tstamp'],
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'saldo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['saldo'],
		),
		//'kontoChecked' => array
		//(
		//	'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['kontoChecked'],
		//),
		'accountChecked' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['accountChecked'],
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'archived' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['archived'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		// Information anzeigen, welches FE-Mitglied dem Spieler zugeordnet ist
		'memberAssign' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['memberAssign'],
			'input_field_callback'    => array('tl_fernschach_spieler', 'getMemberAssign'),
			'exclude'                 => true
		),
		'nachname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['nachname'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'vorname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['vorname'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'filter'                  => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'titel' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['titel'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'anrede' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['anrede'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['anrede_options'],
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'tl_class'            => 'w50 clr'
			),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'briefanrede' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['briefanrede'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['briefanrede_options'],
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(30) NOT NULL default ''"
		),
		'birthday' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['birthday'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'birthplace' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['birthplace'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'sex' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['sex'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['sex_options'],
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'death' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['death'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'clr'
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'deathday' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['deathday'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 12,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'deathplace' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['deathplace'],
			'exclude'                 => true,
			'search'                  => false,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'plz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['plz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'ort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['ort'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'strasse' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['strasse'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'adresszusatz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['adresszusatz'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'plz2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['plz2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'ort2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['ort2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'strasse2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['strasse2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'adresszusatz2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['adresszusatz2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'telefon1' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['telefon1'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'telefon2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['telefon2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'telefax1' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['telefax1'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'telefax2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['telefax2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'email1' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['email1'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50', 'rgxp'=>'email'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'email2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['email2'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50', 'rgxp'=>'email'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'memberId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['memberId'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
				'unique'              => true,
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'memberInternationalId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['memberInternationalId'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'streichung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['streichung'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'alnum',
				'maxlength'           => 10,
				'tl_class'            => 'w50',
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'memberships' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['memberships'],
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
						'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['membership_from'],
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
						'load_callback'           => array
						(
							array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
						),
						'save_callback' => array
						(
							array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
						),
					),
					'to' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['membership_to'],
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
						'load_callback'           => array
						(
							array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
						),
						'save_callback' => array
						(
							array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
						),
					),
					'status' => array
					(
						'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['membership_status'],
						'exclude'               => true,
						'inputType'             => 'text',
						'eval'                  => array
						(
							'style'             => 'width: 300px',
						),
					),
				)
			),
			'sql'                   => "blob NULL"
		),
		'verein' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['verein'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => false,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 80,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(80) NOT NULL default ''"
		),
		'status' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['status'],
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['status_options'],
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'zuzug' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['zuzug'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 12,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'klassenberechtigung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['klassenberechtigung'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'filter'                  => true,
			'options'                 => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['klassenberechtigung_options'],
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'gastNummer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['gastNummer'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'servertesterNummer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['servertesterNummer'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'fremdspielerNummer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fremdspielerNummer'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => false,
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 20,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(20) NOT NULL default ''"
		),
		'fgm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fgm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'fgm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fgm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'sim_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['sim_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'sim_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['sim_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'fim_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fim_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'fim_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fim_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'ccm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['ccm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'ccm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['ccm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'lgm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['lgm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'lgm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['lgm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'cce_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['cce_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'cce_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['cce_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'lim_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['lim_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'lim_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['lim_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'gm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['gm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'gm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['gm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'im_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['im_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'im_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['im_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'wgm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wgm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'wgm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wgm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'fm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'fm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'wim_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wim_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'wim_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wim_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'cm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['cm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'cm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['cm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'wfm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wfm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'wfm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wfm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'wcm_title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wcm_title'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => false,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'wcm_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['wcm_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'titelinfo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['titelinfo'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'search'                  => false,
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'long clr'
			),
			'sql'                     => "text NULL"
		),
		'honor_25' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_25'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_25_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_25_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'honor_40' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_40'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_40_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_40_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'honor_50' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_50'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_50_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_50_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'honor_60' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_60'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_60_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_60_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'honor_70' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_70'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_70_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_70_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'honor_president' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_president'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_president_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_president_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'honor_member' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_member'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'w50 clr',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'honor_member_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['honor_member_date'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'normen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['normen'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'tl_class'            => 'clr',
				'buttonPos'           => 'top',
				'columnFields'        => array
				(
					'title' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['normen_title'],
						'exclude'                 => true,
						'search'                  => false,
						'sorting'                 => true,
						'flag'                    => 12,
						'inputType'               => 'select',
						'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['normen_title_options'],
						'eval'                    => array
						(
							'includeBlankOption'  => true,
							'style'               => 'width: 280px',
						),
					),
					'date' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['normen_date'],
						'exclude'                 => true,
						'search'                  => false,
						'sorting'                 => true,
						'flag'                    => 12,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 10,
							'style'               => 'width: 130px',
							'rgxp'                => 'alnum'
						),
						'load_callback'           => array
						(
							array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
						),
						'save_callback' => array
						(
							array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
						),
					),
					'tournament' => array
					(
						'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['normen_tournament'],
						'exclude'               => true,
						'inputType'             => 'text',
						'eval'                  => array
						(
							'style'             => 'width: 240px',
						),
					),
					'url' => array
					(
						'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['normen_url'],
						'exclude'               => true,
						'inputType'             => 'text',
						'eval'                  => array
						(
							'style'             => 'width: 240px',
						),
					),
				)
			),
			'sql'                   => "blob NULL"
		),
		'inhaber' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['inhaber'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'iban' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['iban'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>22, 'tl_class'=>'w50 clr'),
			'sql'                     => "varchar(22) NOT NULL default ''"
		),
		'bic' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['bic'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'search'                  => false,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>11, 'tl_class'=>'w50'),
			'sql'                     => "varchar(11) NOT NULL default ''"
		),
		'info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['info'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'tl_class'=>'long'),
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['published'],
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
		'fertig' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler']['fertig'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
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
class tl_fernschach_spieler extends \Backend
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
	 * Prüfe Zugangsrechte für tl_fernschach_spieler
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
		if(!$this->User->hasAccess('import', 'fernschach_spieler')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler']['list']['global_operations']['importSpieler']);
		if(!$this->User->hasAccess('export', 'fernschach_spieler')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler']['list']['global_operations']['exportXLS']);
		if(!$this->User->hasAccess('all', 'fernschach_spieler')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler']['list']['global_operations']['all']);
		if(!$this->User->hasAccess('create', 'fernschach_spieler')) $GLOBALS['TL_DCA']['tl_fernschach_spieler']['config']['closed'] = true;

		// Aktuelle Aktion von act prüfen
		switch (Input::get('act'))
		{
			case 'create': // Spieler anlegen
				if(!$this->User->hasAccess('create', 'fernschach_spieler'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um einen neuen Spieler anzulegen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'copy': // Spieler kopieren
				if(!$this->User->hasAccess('copy', 'fernschach_spieler'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um einen Spieler zu kopieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'toggle': // Spieler aktivieren/deaktivieren
				if(!$this->User->hasAccess('toggle', 'fernschach_spieler'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Spieler zu (de)aktivieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'show': // Infobox
				if(!$this->User->hasAccess('show', 'fernschach_spieler'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Spieler-Infobox anzuzeigen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'edit': // Spieler bearbeiten
				if(!$this->User->hasAccess('edit', 'fernschach_spieler'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um einen Spieler zu bearbeiten.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			default:
				// Aktuelle Aktion von key prüfen
				switch (Input::get('key'))
				{
					case 'importSpieler': // Spieler importieren
						if(!$this->User->hasAccess('import', 'fernschach_spieler'))
						{
							$this->log('Fernschach-Verwaltung: Keine Rechte, um Spieler zu importieren.', __METHOD__, TL_ERROR);
							$this->redirect('contao/main.php?act=error');
						}
						break;

					case 'exportXLS': // Spieler exportieren
						if(!$this->User->hasAccess('export', 'fernschach_spieler'))
						{
							$this->log('Fernschach-Verwaltung: Keine Rechte, um Spieler zu exportieren.', __METHOD__, TL_ERROR);
							$this->redirect('contao/main.php?act=error');
						}
						break;

					default:
				}

				// Buchungen eines Spielers aufgerufen?
				if(Input::get('do') == 'fernschach-spieler' && Input::get('table') == 'tl_fernschach_spieler_konto' && Input::get('id') > 0 && !Input::get('act'))
				{
					if(!$this->User->hasAccess('konto', 'fernschach_spieler'))
					{
						$this->log('Fernschach-Verwaltung: Keine Rechte, um die Buchungen eines Spieler anzusehen.', __METHOD__, TL_ERROR);
						$this->redirect('contao/main.php?act=error');
					}
				}
		}
		
		// Berechtigungen Infobox setzen
		foreach($GLOBALS['TL_DCA']['tl_fernschach_spieler']['fields'] as $key => $value)
		{
			if(!$this->User->hasAccess('tl_fernschach_spieler::'.$key, 'alexf'))
			{
				// Benutzer Zugriff auf dieses Feld sperren
				$GLOBALS['TL_DCA']['tl_fernschach_spieler']['fields'][$key]['eval']['doNotShow'] = true;

				// Zusätzlich Toggle abschalten, wenn published nicht erlaubt ist
				if($key == 'published')
				{
					unset($GLOBALS['TL_DCA']['tl_fernschach_spieler']['list']['operations']['toggle']);
				}

				// Zusätzlich FertigIcon abschalten, wenn fertig nicht erlaubt ist
				if($key == 'fertig')
				{
					unset($GLOBALS['TL_DCA']['tl_fernschach_spieler']['list']['operations']['fertigIcon']);
				}
			}
		}

	}

	/**
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listMembers($row, $label, Contao\DataContainer $dc, $args)
	{
		//echo "<pre>";
		//print_r($row);
		//echo "</pre>";
		$args[3] = \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($args[3]); // Geburtstag von JJJJMMTT umwandeln in TT.MM.JJJJ

		if($row['archived'])
		{
			for($x = 0; $x < count($args); $x++)
			{
				$args[$x] = '<span style="color:#B6B6B6;">'.$args[$x].'</span>';
			}
		}

		// Buchungen des Spielers prüfen
		//\Schachbulle\ContaoFernschachBundle\Classes\Helper::checkResetbuchungen($row['id']);

		// Kontostand ausgeben
		$salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($row['id']);
		//print_r($salden);
		if($salden)
		{
			$value = end($salden);
			$wert = str_replace('.', ',', sprintf('%0.2f', $value));
			if($value > 0)
			{
				$html = '<span style="color:green;">';
				$html .= $wert.' €';
				$html .= '<span>';
			}
			elseif($value < 0)
			{
				$html = '<span style="color:red;">';
				$html .= $wert.' €';
				$html .= '<span>';
			}
			else
			{
				$html = $wert.' €';
			}
			$args[6] = $html;
		}
		else $args[6] = '&nbsp;';

		// Kontoprüfung ausgeben
		$checked = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkKonto($row['id']);
		if($checked) $args[7] = '<img title="Das Konto wurde geprüft (Resetbuchung ab 01.04.2023 vorhanden)." src="bundles/contaofernschach/images/ja.png" width="12" align="middle">';
		else $args[7] = '<img title="Das Konto wurde noch nicht geprüft (Resetbuchung ab 01.04.2023 nicht vorhanden)." src="bundles/contaofernschach/images/nein.png" width="12" align="middle">';

		// Zugriffsrechte auf Felder prüfen
		if(!$this->User->hasAccess('tl_fernschach_spieler::memberId', 'alexf')) $args[0] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('tl_fernschach_spieler::nachname', 'alexf')) $args[1] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('tl_fernschach_spieler::vorname', 'alexf')) $args[2] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('tl_fernschach_spieler::birthday', 'alexf')) $args[3] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('tl_fernschach_spieler::plz', 'alexf')) $args[4] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('tl_fernschach_spieler::ort', 'alexf')) $args[5] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('saldo', 'fernschach_spieler')) $args[6] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
		if(!$this->User->hasAccess('accountChecked', 'fernschach_spieler')) $args[7] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';

		// Zugriffsrechte auf weitere Sortierfelder prüfen
		$session = \Session::getInstance()->getData();
		switch($session['sorting']['tl_fernschach_spieler'])
		{
			case 'fremdspielerNummer':
			case 'gastNummer':
			case 'memberInternationalId':
			case 'servertesterNummer':
				if(!$this->User->hasAccess('tl_fernschach_spieler::'.$session['sorting']['tl_fernschach_spieler'], 'alexf')) $args[8] = '<span title="Kein Zugriff">&bull;&bull;&bull;</span>';
				break;
			default:
		}

		// Datensatz komplett zurückgeben
		return $args;
	}

	public function generateAdvancedFilter(DataContainer $dc)
	{

		if(\Input::get('id') > 0) return '';

		$session = \Session::getInstance()->getData();

		// Filters
		$arrFilters = array
		(
			'tfs_filter'   => array
			(
				'name'    => 'tfs_filter',
				'label'   => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_extended'],
				'options' => array
				(
					'1'   => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members'],
					'2'   => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_birthday_failed'],
					'3'   => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_iccf_failed'],
					'4'   => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_mail_failed'],
					'8'   => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_none_members'],
					'101' => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearNext'],
					'100' => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearThis'],
					'99'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus1'],
					'98'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus2'],
					'97'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus3'],
					'96'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus4'],
					'95'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus5'],
					'94'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus6'],
					'93'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus7'],
					'92'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus8'],
					'91'  => $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter_active_members_yearMinus9'],
				)
			),
		);

        $strBuffer = '
<div class="tl_filter tfs_filter tl_subpanel">
<strong>' . $GLOBALS['TL_LANG']['tl_fernschach_spieler']['filter'] . ':</strong> ' . "\n";

        // Generate filters
        foreach ($arrFilters as $arrFilter)
        {
            $strOptions = '
  <option value="' . $arrFilter['name'] . '">' . $arrFilter['label'] . '</option>
  <option value="' . $arrFilter['name'] . '">---</option>' . "\n";

            // Generate options
            foreach ($arrFilter['options'] as $k => $v)
            {
                $strOptions .= '  <option value="' . $k . '"' . (($session['filter']['tl_fernschach_spielerFilter'][$arrFilter['name']] === (string) $k) ? ' selected' : '') . '>' . $v . '</option>' . "\n";
            }

            $strBuffer .= '<select name="' . $arrFilter['name'] . '" id="' . $arrFilter['name'] . '" class="tl_select' . (isset($session['filter']['tl_fernschach_spielerFilter'][$arrFilter['name']]) ? ' active' : '') . '">
' . $strOptions . '
</select>' . "\n";
        }

        return $strBuffer . '</div>';

	}

	public function applyAdvancedFilter()
	{

		$session = \Session::getInstance()->getData();

		// Filterwerte in der Sitzung speichern
		foreach($_POST as $k => $v)
		{
			if(substr($k, 0, 4) != 'tfs_')
			{
				continue;
			}

			// Filter zurücksetzen
			if($k == \Input::post($k))
			{
				unset($session['filter']['tl_fernschach_spielerFilter'][$k]);
			}
			// Filter zuweisen
			else
			{
				$session['filter']['tl_fernschach_spielerFilter'][$k] = \Input::post($k);
			}
		}

		$this->Session->setData($session);

		if(\Input::get('id') > 0 || !isset($session['filter']['tl_fernschach_spielerFilter']))
		{
			return;
		}

		$arrPlayers = null;

		switch($session['filter']['tl_fernschach_spielerFilter']['tfs_filter'])
		{
			case '1': // Alle Mitglieder
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ?")
				                                      ->execute('');
				$arrPlayers = array();
				if($objPlayers->numRows)
				{
					while($objPlayers->next())
					{
						// Mitgliedschaften prüfen (memberships)
						$aktiv = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayers->memberships);
						if($aktiv) $arrPlayers[] = $objPlayers->id;
					}
				}
				break;

			case '2': // Geburtsdatum fehlt
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE birthday = ? OR birthday = ?")
				                                      ->execute(0, '');
				$arrPlayers = is_array($arrPlayers) ? array_intersect($arrPlayers, $objPlayers->fetchEach('id')) : $objPlayers->fetchEach('id');
				break;

			case '3': // ICCF-Nummer fehlt
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE memberInternationalId = ?")
				                                      ->execute('');
				$arrPlayers = is_array($arrPlayers) ? array_intersect($arrPlayers, $objPlayers->fetchEach('id')) : $objPlayers->fetchEach('id');
				break;

			case '4': // E-Mail fehlt
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE email1 = ? AND email2 = ?")
				                                      ->execute('', '');
				$arrPlayers = is_array($arrPlayers) ? array_intersect($arrPlayers, $objPlayers->fetchEach('id')) : $objPlayers->fetchEach('id');
				break;

			case '5': // Mitgliedsende 31.12. letztes Jahr
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ?")
				                                      ->execute('');
				$arrPlayers = array();
				if($objPlayers->numRows)
				{
					$datum = (date('Y') - 1).'1231';
					while($objPlayers->next())
					{
						// Ende einer Mitgliedschaft suchen (memberships)
						$aktiv = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchMembership($objPlayers->memberships, $datum);
						if($aktiv) $arrPlayers[] = $objPlayers->id;
					}
				}
				break;

			case '6': // Mitgliedsende 31.12. dieses Jahr
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ?")
				                                      ->execute('');
				$arrPlayers = array();
				if($objPlayers->numRows)
				{
					$datum = date('Y').'1231';
					while($objPlayers->next())
					{
						// Ende einer Mitgliedschaft suchen (memberships)
						$aktiv = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchMembership($objPlayers->memberships, $datum);
						if($aktiv) $arrPlayers[] = $objPlayers->id;
					}
				}
				break;

			case '7': // Mitgliedsende 31.12. nächstes Jahr
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ?")
				                                      ->execute('');
				$arrPlayers = array();
				if($objPlayers->numRows)
				{
					$datum = (date('Y') + 1).'1231';
					while($objPlayers->next())
					{
						// Ende einer Mitgliedschaft suchen (memberships)
						$aktiv = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchMembership($objPlayers->memberships, $datum);
						if($aktiv) $arrPlayers[] = $objPlayers->id;
					}
				}
				break;

			case '101': // Mitgliedsende 31.12. nächstes Jahr
			case '100': // Mitgliedsende 31.12. dieses Jahr
			case '99': // Mitgliedsende 31.12. letztes Jahr
			case '98': // Mitgliedsende 31.12. minus 2 Jahre
			case '97': // Mitgliedsende 31.12. minus 3 Jahre
			case '96': // Mitgliedsende 31.12. minus 4 Jahre
			case '95': // Mitgliedsende 31.12. minus 5 Jahre
			case '94': // Mitgliedsende 31.12. minus 6 Jahre
			case '93': // Mitgliedsende 31.12. minus 7 Jahre
			case '92': // Mitgliedsende 31.12. minus 8 Jahre
			case '91': // Mitgliedsende 31.12. minus 9 Jahre
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler")
				                                      ->execute();
				$arrPlayers = array();
				if($objPlayers->numRows)
				{
					$datum = (date('Y') + $session['filter']['tl_fernschach_spielerFilter']['tfs_filter'] - 100).'1231';
					while($objPlayers->next())
					{
						// Ende einer Mitgliedschaft suchen (memberships)
						$found = \Schachbulle\ContaoFernschachBundle\Classes\Helper::searchMembership($objPlayers->memberships, $datum);
						if($found) $arrPlayers[] = $objPlayers->id;
					}
				}
				break;

			case '8': // Alle Nichtmitglieder
				$objPlayers = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_spieler WHERE archived = ?")
				                                      ->execute('');
				$arrPlayers = array();
				if($objPlayers->numRows)
				{
					while($objPlayers->next())
					{
						// Mitgliedschaften prüfen (memberships)
						$aktiv = \Schachbulle\ContaoFernschachBundle\Classes\Helper::checkMembership($objPlayers->memberships);
						if(!$aktiv) $arrPlayers[] = $objPlayers->id;
					}
				}
				break;

			default:

		}

		if(is_array($arrPlayers) && empty($arrPlayers))
		{
			$arrPlayers = array(0);
		}

		$log = print_r($arrPlayers, true);
		log_message($log, 'fernschachverwaltung.log');

		$GLOBALS['TL_DCA']['tl_fernschach_spieler']['list']['sorting']['root'] = $arrPlayers;

	}

	/**
	 * Zugeordnete Frontend-Mitglieder anzeigen
	 * @param DataContainer $dc
	 *
	 * @return string HTML-Code
	 */
	public function getMemberAssign(DataContainer $dc)
	{
		if($dc->activeRecord->id)
		{
			// Spieler-ID in tl_member.fernschach_memberId suchen
			$objMembers = \MemberModel::findBy('fernschach_memberId', $dc->activeRecord->id);
			$status = '';
			$zaehler = 0;
			if($objMembers)
			{
				foreach($objMembers as $objMember)
				{
					$zaehler++;
					$status .= '['.$zaehler.'] <b>'.$objMember->username.'</b><br>';
				}
			}
			else
			{
				$status = $GLOBALS['TL_LANG']['tl_fernschach_spieler']['memberAssign_Failed'][0];
			}

			return '
			<div class="tl_listing_container list_view" id="tl_listing">
				<table class="tl_listing">
					<tbody>
						<tr class="even click2edit toggle_select hover-row">
							<td class="tl_file_list" width="50%">'.$GLOBALS['TL_LANG']['tl_fernschach_spieler']['memberAssign_Headline'][0].'</td>
							<td class="tl_file_list">'.$status.'</td>
						</tr>
					</tbody>
				</table>
			</div>';
		}

	}

	/**
	 * Gibt den Edit-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateEditButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('edit', 'fernschach_spieler')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Copy-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateCopyButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('copy', 'fernschach_spieler')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Delete-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateDeleteButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('delete', 'fernschach_spieler')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Toggle-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateToggleButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('toggle', 'fernschach_spieler')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	/**
	 * Gibt den Show-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateShowButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('show', 'fernschach_spieler')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'&amp;popup=1" title="'.specialchars($title).'"'.$attributes.' onclick="Backend.openModalIframe({\'title\':\''.str_replace('%s', $row['id'], $GLOBALS['TL_LANG']['tl_fernschach_spieler']['show'][1]).'\',\'url\':this.href});return false" >'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';


	}

	/**
	 * Gibt den Konto-Button zurück
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function generateKontoButton($row, $href, $label, $title, $icon, $attributes)
	{
		return($this->User->isAdmin || $this->User->hasAccess('konto', 'fernschach_spieler')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.png$/i', '_.png', $icon)).' ';
	}

}
