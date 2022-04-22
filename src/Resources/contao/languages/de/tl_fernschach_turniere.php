<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['tournament_legend'] = 'Turnier';
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['titel'] = array('Titel', 'Geben Sie hier den Turniertitel ein');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['kennziffer'] = array('Kennzeichen', 'Kennzeichen bzw. Kennziffer des Turniers/der Turnierserie');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['registrationDate'] = array('Meldedatum', 'Meldedatum lt. Ausschreibung im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['startDate'] = array('Startdatum', 'Startdatum lt. Ausschreibung im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['typ'] = array('Turniertyp', 'Typ des Turniers');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['nenngeld'] = array('Nenngeld', 'Nenngeld in Euro');

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['meldung_legend'] = 'Online-Anmeldung';
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['onlineAnmeldung'] = array('Online-Anmeldung', 'Turnier im Online-Meldeformular anzeigen.');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerMax'] = array('Maximale Spieleranzahl', 'Maximale Anzahl der Spieler für dieses Turnier. 0 = unbegrenzt.');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art'] = array('Turnierart', 'Art des Turniers');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['artInfo'] = array('Alternative Turnierart', 'Eigener Text für die Turnierart. Wird nur im Online-Meldeformular verwendet, wenn keine Turnierart ausgewählt wurde.');

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiter_legend'] = 'Turnierleiter';
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterName'] = array('Turnierleiter', 'Name des Turnierleiters');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterEmail'] = array('E-Mail', 'E-Mail-Adresse des Turnierleiters');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterUserId'] = array('Benutzer', 'Zugeordneter Backend-Benutzer, der das Turnier bearbeiten darf.');

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applications_legend'] = 'Bewerbungen';
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['name'] = array('Spieler', 'Name des Spielers');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applicationDate'] = array('Bewerbung am', 'Datum der Bewerbung');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['state'] = array('Status', 'Status der Zu- oder Absage');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['promiseDate'] = array('Datum', 'Datum der Zu- oder Absage');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applicationText'] = array('Hinweistext', 'Tragen Sie hier Informationen und Links zum Turnier ein. Dieser Text wird im Frontend bei den Zusagen angezeigt.');

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier das Turnier');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['new'] = array('Neues Turnier', 'Neues Turnier anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['edit'] = array('Turnier bearbeiten', 'Turnier %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['copy'] = array('Turnier kopieren', 'Turnier %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['delete'] = array('Turnier löschen', 'Turnier %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['toggle'] = array('Turnier aktivieren/deaktivieren', 'Turnier %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['show'] = array('Turnierdetails anzeigen', 'Details des Turnier %s anzeigen');

/**
 * Buttons für globale Operationen
 */
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['importTurniere'] = array('Import Turniere', 'Import von neuen Turnieren bzw. vorhandene Turniere ergänzen.');


/**
 * Sonstige Felder
 */

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['bewerbungen'] = array('Bewerbungen', 'Anzahl der Bewerbungen');
$GLOBALS['TL_LANG']['tl_fernschach_turniere']['zusagen'] = array('Status', 'Anzahl der Zu- oder Absagen');

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['typ_options'] = array
(
	'n' => 'Nationales Turnier',
	'i' => 'Internationales Turnier',
	'e' => 'Einladungsturnier',
);

$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art_options'] = array
(
	'k' => 'Klassen- bzw. Aufstiegsturnier',
	't' => 'Thematurnier',
	'a' => 'Allgemeines Kleinturnier',
	'l' => 'Länderkampf',
	'9' => 'Schach 960',
);
