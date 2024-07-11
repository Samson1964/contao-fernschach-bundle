<?php

/**
 * Table tl_fernschach_spieler_konto
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler_konto'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_spieler',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			array('tl_fernschach_spieler_konto', 'checkPermission'),
			array('tl_fernschach_spieler_konto', 'getInfo'),
			array('tl_fernschach_spieler_konto', 'checkSaldo')
		),
		'sql' => array
		(
			'keys' => array
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
			'mode'                    => 2,
			'fields'                  => array('datum DESC', 'sortierung DESC'),
			'flag'                    => 3,
			'panelLayout'             => 'filter;sort;search,limit',
			'disableGrouping'         => true
		),
		'label' => array
		(
			'fields'                  => array('datum', 'sortierung', 'betrag', 'kategorie', 'art', 'verwendungszweck', 'saldo'),
			'showColumns'             => true,
			'label_callback'          => array('tl_fernschach_spieler_konto', 'getLabel')
		),
		'global_operations' => array
		(
			'verschiebeBuchungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verschiebeBuchungen'],
				'href'                => 'key=verschiebeBuchungen',
				'icon'                => 'bundles/contaofernschach/images/move.png'
			),
			'importBuchungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['importBuchungen'],
				'href'                => 'key=importBuchungen',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
				'button_callback'     => array('tl_fernschach_spieler_konto', 'generateEditButton')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg',
				'button_callback'     => array('tl_fernschach_spieler_konto', 'generateCopyButton')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_fernschach_spieler_konto', 'generateDeleteButton')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['toggle'],
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'           => 'published',
					'options'         => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
				//'button_callback'     => array('tl_fernschach_spieler_konto', 'generateToggleButton')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['show'],
				'href'                => 'act=show',
				//'attributes'          => 'onclick="Backend.openModalIframe({\'title\':\'Details anzeigen\',\'url\':this.href});return false"',
				'icon'                => 'bundles/contaofernschach/images/show.svg',
				//'button_callback'     => array('tl_fernschach_spieler_konto', 'generateShowButton')
			),
			'markiertIcon' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['markiertIcon'],
				'attributes'          => 'onclick="markiertSetzen(this, %s); javascript:alert(\'Hallo\'); Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'           => 'markierung',
					'options'         => array
					(
						array('value' => '', 'icon' => 'bundles/contaofernschach/images/unfertig.png'),
						array('value' => '1', 'icon' => 'bundles/contaofernschach/images/fertig.png'),
					)
				),
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{buchung_legend},betrag,typ,datum,sortierung,kategorie,art,verwendungszweck;{extras_legend},markierung,saldoReset;{turnier_legend:hide},turnier;{comment_legend:hide},comment;{connection_legend},meldungId;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_fernschach_spieler.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager') // Elterntabelle benötigt ein Model, wenn diese Tabelle eine Relation und ein Model hat
		),
		'tstamp' => array
		(
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'resetRecord' => array
		(
			'exclude'                 => true,
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'importDate' => array
		(
			'exclude'                 => true,
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'betrag' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['betrag'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit',
				'mandatory'           => false,
				'tl_class'            => 'w50',
				'maxlength'           => 6
			),
			'load_callback'           => array
			(
				array('tl_fernschach_spieler_konto', 'getBetrag')
			),
			'save_callback' => array
			(
				array('tl_fernschach_spieler_konto', 'putBetrag')
			),
			'sql'                     => "varchar(6) NOT NULL default ''"
		),
		'typ' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['typ_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'multiple'            => false,
				'mandatory'           => true,
				'includeBlankOption'  => false
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'datum' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['datum'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_spieler_konto', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'sortierung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['sortierung'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'tl_class'            => 'w50',
				'maxlength'           => 5,
				'rgxp'                => 'digit'
			),
			'sql'                     => "int(5) unsigned NOT NULL default 0"
		),
		'kategorie' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['kategorie'],
			'exclude'                 => true,
			'sorting'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['kategorie_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50 clr',
				'multiple'            => false,
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'art' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'multiple'            => false,
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'verwendungszweck' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verwendungszweck'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'tl_class'=>'w50', 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'markierung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['markierung'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'saldoReset' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['saldoReset'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'turnier' => array
		(
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_fernschach_spieler_konto', 'getTurniere'),
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'includeBlankOption'  => true,
				'chosen'              => true,
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'comment' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'tl_class'            => 'w50 noresize',
			),
			'sql'                     => "text NULL"
		),
		'meldungId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['meldungId'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['published'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'default'                 => 1,
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'saldo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['saldo'],
		),
	)
);

/**
 * Class tl_fernschach_spieler_konto
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_spieler_konto extends \Backend
{

	var $turniere = array();
	var $salden = array();
	var $records = 0;

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * onload_callback: Fuehrt eine Aktion bei der Initialisierung des DataContainer-Objekts aus.
	 * @param $dc
	 */
	public function checkSaldo(\DataContainer $dc)
	{
		$id = strlen(\Input::get('id')) ? \Input::get('id') : $dc->currentPid;

		// Globale Resetbuchungen prüfen
		$reset = new \Schachbulle\ContaoFernschachBundle\Classes\Konto\ResetUtil();
		$reset->Pruefung('h', $id);

		// Salden berechnen
		$this->salden = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSaldo($id);
		//print_r($this->salden);
		return;

	}

	/**
	 * Prüfe Zugangsrechte für tl_fernschach_spieler
	 *
	 * @throws AccessDeniedException
	 */
	public function checkPermission(\DataContainer $dc)
	{
		if($this->User->isAdmin)
		{
			return;
		}

		// Zugriff auf globale Operationen prüfen
		if(!$this->User->hasAccess('import', 'fernschach_konto')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler_konto']['list']['global_operations']['importBuchungen']);
		if(!$this->User->hasAccess('all', 'fernschach_konto')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler_konto']['list']['global_operations']['all']);
		if(!$this->User->hasAccess('create', 'fernschach_konto')) $GLOBALS['TL_DCA']['tl_fernschach_spieler_konto']['config']['closed'] = true;

		// Aktuelle Aktion von act prüfen
		switch(\Input::get('act'))
		{
			case 'create': // Buchung anlegen
				if(!$this->User->hasAccess('create', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine neue Buchung anzulegen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'copy': // Buchung kopieren
				if(!$this->User->hasAccess('copy', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu kopieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'toggle': // Buchung aktivieren/deaktivieren
				if(!$this->User->hasAccess('toggle', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu (de)aktivieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'show': // Infobox
				if(!$this->User->hasAccess('show', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung-Infobox anzuzeigen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'edit': // Buchung bearbeiten
				if(!$this->User->hasAccess('edit', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu bearbeiten.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			default:

				// Aktuelle Aktion von key prüfen
				switch(\Input::get('key'))
				{
					case 'import': // Buchungen importieren
						if(!$this->User->hasAccess('import', 'fernschach_konto'))
						{
							$this->log('Fernschach-Verwaltung: Keine Rechte, um Buchungen zu importieren.', __METHOD__, TL_ERROR);
							$this->redirect('contao/main.php?act=error');
						}
						break;

					default:
				}
		}

	}

	/**
	 * Datensätze modifiziert auflisten
	 * @param array $arrRow    Array mit dem gesamten Datensatz
	 * @param string $label    String aus dem Feld datum
	 * @param array $args      Array mit dem Teildatensatz (gewünschte Felder in gewünschter Reihenfolge)
	 * @return array
	 */
	public function getLabel($arrRow, $label, \DataContainer $dc, $args)
	{
		if(!count($this->salden)) self::checkSaldo($dc);

		$css = 'display:block; ';
		// Buchung positiv oder negativ?
		switch($arrRow['typ'])
		{
			case 'h': $css .= 'color:green; '; break;
			case 's': $css .= 'color:red; '; break;
			default: $css .= 'color:yellow; '; break;
		}

		// Resetbuchung = true (schwarze Linie unten)
		if($arrRow['resetRecord'])
			$css .= 'border-bottom:2px red solid; padding-bottom:2px; ';
		elseif($arrRow['saldoReset'])
			$css .= 'border-bottom:2px black solid; padding-bottom:2px; ';

		// Markieren = true (rötliche Hintergrundfarbe)
		if($arrRow['markierung']) $css .= 'background-color:#FFE8DD; ';

		$args[0] = '<span style="'.$css.'">'.$args[0].'</span>';
		$args[1] = '<span style="'.$css.'">'.($args[1] ? $args[1] : '-').'</span>';
		$args[2] = '<span style="white-space:nowrap;'.$css.'">'.self::getEuro($args[2], $arrRow['typ']).'</span>';
		$args[3] = '<span style="'.$css.'">'.$args[3].'&nbsp;</span>';
		$args[4] = '<span style="white-space:nowrap;'.$css.'">'.$args[4].'&nbsp;</span>';
		$args[5] = '<span style="'.$css.'">'.$args[5].'&nbsp;</span>';
		if(isset($this->salden[$arrRow['id']])) $args[6] = '<span style="white-space:nowrap; font-weight:bold;'.$css.'">'.self::getEuro($this->salden[$arrRow['id']]).'</span>';

		return $args;
	}

	/**
	 * Zahl in Euro-Betrag umwandeln
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	public function getEuro($value, $typ = false)
	{
		// Komma umwandeln in Punkt
		$value = str_replace(',', '.', $value);

		// Farbe bestimmen
		if($typ)
		{
			if($typ == 'h') $html = '<span style="color:green;">';
			elseif($typ == 's') $html = '<span style="color:red;">';
			else $html = '<span>';
		}
		else
		{
			if($value > 0) $html = '<span style="color:green;">';
			elseif($value < 0) $html = '<span style="color:red;">';
			else $html = '<span>';
		}

		// Betrag formatieren und zurückgeben
		$value = str_replace('.', ',', sprintf('%0.2f', $value));
		return $html.$value.' €</span>';
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
	 * options_callback: Ermöglicht das Befüllen eines Drop-Down-Menüs oder einer Checkbox-Liste mittels einer individuellen Funktion.
	 * @param  $dc
	 * @return array
	 */
	public function getTurniere(\DataContainer $dc)
	{
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere ORDER BY title ASC")
		                                       ->execute();

		$arr = array();
		if($objTurniere->numRows)
		{
			while($objTurniere->next())
			{
				$arr[$objTurniere->id] = $objTurniere->title.' (Nenngeld: '.self::getEuro($objTurniere->nenngeld).')';
			}
		}
		return $arr;
	}

	public function getTurnier($id)
	{
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE id = ?")
		                                       ->execute($id);

		if($objTurniere->numRows)
		{
			return $objTurniere->title;
		}
		return false;
	}

	/**
	 * Betrag für Datenbank umwandeln
	 * @param $varValue       string      z.B. 9,12
	 * @return                float       z.B. 9.12
	 */
	public function putBetrag($varValue)
	{
		$temp = str_replace(',', '.', $varValue); // Komma in Punkt umwandeln
		$temp = abs($temp); // Vorzeichen entfernen
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
		return($this->User->isAdmin || $this->User->hasAccess('edit', 'fernschach_konto')) ? '<a href="'.\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
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
		return($this->User->isAdmin || $this->User->hasAccess('copy', 'fernschach_konto')) ? '<a href="'.\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
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
		return($this->User->isAdmin || $this->User->hasAccess('delete', 'fernschach_konto')) ? '<a href="'.\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
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
		return($this->User->isAdmin || $this->User->hasAccess('toggle', 'fernschach_konto')) ? '<a href="'.\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
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
		return($this->User->isAdmin || $this->User->hasAccess('show', 'fernschach_konto')) ? '<a href="'.\Backend::addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
	}

	public function getInfo(\DataContainer $dc)
	{
		if($_POST || Input::get('act') != 'edit')
		{
			return;
		}
	
		$objRecord = \Schachbulle\ContaoFernschachBundle\Models\Hauptkonto::findByPk($dc->id);
		if($objRecord === null)
		{
			return;
		}
	
		if($objRecord->resetRecord)
		{
			\Message::addInfo($GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['message_resetRecord']);
			//Message::addError("Fehlermeldung.");
			//Message::addConfirmation("Bestätigungsmeldung");
		}
	}

}
