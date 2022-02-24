<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['application_legend'] = 'Bewerbung';
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['tournament'] = array('Turnier', 'Wählen Sie das Turnier aus, für das sich der Spieler beworben hat.');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['applicationDate'] = array('Bewerbungsdatum', 'Bewerbungsdatum im Format TT.MM.JJJJ');

$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['promise_legend'] = 'Zu- oder Absage';
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['state'] = array('Status', 'Status der Zu- oder Absage');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['promiseDate'] = array('Datum', 'Zu- oder Absagedatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier das Turnier');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['new'] = array('Neue Bewerbung', 'Neues Bewerbung anlegen');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['edit'] = array('Bewerbung bearbeiten', 'Bewerbung %s bearbeiten');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['copy'] = array('Bewerbung kopieren', 'Bewerbung %s kopieren');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['delete'] = array('Bewerbung löschen', 'Bewerbung %s löschen');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['toggle'] = array('Bewerbung aktivieren/deaktivieren', 'Bewerbung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['show'] = array('Bewerbungsdetails anzeigen', 'Details der Bewerbung %s anzeigen');

/**
 * Buttons für globale Operationen
 */

$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['members'] = array('Mitglieder', 'Mitglieder verwalten');

/**
 * Optionsfelder
 */

$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_applications']['state_options'] = array
(
	'0' => array('ohne Entscheidung', 'ohne Entscheidung'),
	'1' => array('Zusage', 'Zusage'),
	'2' => array('Absage', 'Absage'),
);
