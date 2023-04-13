<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['application_legend'] = 'Bewerbung';
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['vorname'] = array('Vorname', 'Vorname des Bewerbers. Falls leer, wird der Vorname beim Speichern automatisch anhand des unten ausgewählten Spielers eingetragen.');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['nachname'] = array('Nachname', 'Nachname des Bewerbers. Falls leer, wird der Nachname beim Speichern automatisch anhand des unten ausgewählten Spielers eingetragen.');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['applicationDate'] = array('Bewerbungsdatum', 'Bewerbungsdatum im Format TT.MM.JJJJ');

$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['player_legend'] = 'Zuordnung';
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['spielerId'] = array('Spieler/Mitglied', 'Wählen Sie einen Spieler aus, dem diese Bewerbung zugeordnet wird. Der ausgewählte Spieler wird automatisch in den obigen Feldern Vorname und Nachname gespeichert, wenn diese leer sind.');

$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['promise_legend'] = 'Zu- oder Absage';
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['state'] = array('Status BdF', 'Status der Zu- oder Absage vom BdF');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['stateOrganizer'] = array('Zusage Veranstalter', 'Nur wenn diese Checkbox aktiviert ist, wird der Spieler im Frontend angezeigt.');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['promiseDate'] = array('Datum', 'Zu- oder Absagedatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier das Turnier');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['new'] = array('Neue Bewerbung', 'Neues Bewerbung anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['edit'] = array('Bewerbung bearbeiten', 'Bewerbung %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['copy'] = array('Bewerbung kopieren', 'Bewerbung %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['delete'] = array('Bewerbung löschen', 'Bewerbung %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['toggle'] = array('Bewerbung aktivieren/deaktivieren', 'Bewerbung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['show'] = array('Bewerbungsdetails anzeigen', 'Details der Bewerbung %s anzeigen');

/**
 * Buttons für globale Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['members'] = array('Spieler', 'Spieler verwalten');

/**
 * Optionsfelder
 */

$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['state_options'] = array
(
	'0' => array('ohne Entscheidung', 'ohne Entscheidung'),
	'1' => array('Zusage', 'Zusage'),
	'2' => array('Absage', 'Absage'),
);
