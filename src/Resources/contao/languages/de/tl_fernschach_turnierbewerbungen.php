<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['application_legend'] = 'Bewerbung';
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['tournament'] = array('Turnier', 'Wählen Sie das Turnier aus, für das sich der Spieler beworben hat.');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['applicationDate'] = array('Bewerbungsdatum', 'Bewerbungsdatum im Format TT.MM.JJJJ');

$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['promise_legend'] = 'Zu- oder Absage';
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['state'] = array('Status', 'Status der Zu- oder Absage');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['promiseDate'] = array('Datum', 'Zu- oder Absagedatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier das Turnier');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['new'] = array('Neue Bewerbung', 'Neues Bewerbung anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['edit'] = array('Bewerbung bearbeiten', 'Bewerbung %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['copy'] = array('Bewerbung kopieren', 'Bewerbung %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['delete'] = array('Bewerbung löschen', 'Bewerbung %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['toggle'] = array('Bewerbung aktivieren/deaktivieren', 'Bewerbung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['show'] = array('Bewerbungsdetails anzeigen', 'Details der Bewerbung %s anzeigen');

/**
 * Buttons für globale Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['members'] = array('Spieler', 'Spieler verwalten');

/**
 * Optionsfelder
 */

$GLOBALS['TL_LANG']['tl_fernschach_turnierbewerbungen']['state_options'] = array
(
	'0' => array('ohne Entscheidung', 'ohne Entscheidung'),
	'1' => array('Zusage', 'Zusage'),
	'2' => array('Absage', 'Absage'),
);
