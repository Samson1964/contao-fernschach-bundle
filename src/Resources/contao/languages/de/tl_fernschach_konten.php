<?php

$GLOBALS['TL_LANG']['tl_fernschach_konten']['mainTitle'] = 'Konten';

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_konten']['title_legend'] = 'Kontobezeichnung und Kontotyp';
$GLOBALS['TL_LANG']['tl_fernschach_konten']['title'] = array('Name', 'Geben Sie hier den Namen des Kontos ein.');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['type'] = array('Typ', 'Typ des Kontos');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['placeholder'] = array('Platzhalter', 'Konto nur als Platzhalter verwenden, nicht für Buchungen.');

$GLOBALS['TL_LANG']['tl_fernschach_konten']['description_legend'] = 'Kontobeschreibung';
$GLOBALS['TL_LANG']['tl_fernschach_konten']['description'] = array('Beschreibung', 'Geben Sie hier eine Kontobeschreibung ein');

$GLOBALS['TL_LANG']['tl_fernschach_konten']['balance_legend'] = 'Anfangsbestand';
$GLOBALS['TL_LANG']['tl_fernschach_konten']['openingBalance'] = array('Anfangsbestand', 'Geben Sie hier den Anfangsbestand ein.');

$GLOBALS['TL_LANG']['tl_fernschach_konten']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_konten']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier den Eintrag.');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_konten']['initAccounts'] = array('Standardkonten anlegen', 'Legt einen einfachen Standardkontorahmen an, wenn es noch keine Konten gibt.');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['initAccounts_confirm'] = 'Wollen Sie wirklich einen Standardkontorahmen anlegen?';
$GLOBALS['TL_LANG']['tl_fernschach_konten']['new'] = array('Neues Konto', 'Neues Konto anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['edit'] = array('Konto bearbeiten', 'Konto %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['copy'] = array('Konto kopieren', 'Konto %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['delete'] = array('Konto löschen', 'Konto %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['toggle'] = array('Konto aktivieren/deaktivieren', 'Konto %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['show'] = array('Kontodetails anzeigen', 'Details des Kontos %s anzeigen');

/**
 * Sonstige Felder
 */

$GLOBALS['TL_LANG']['tl_fernschach_konten']['bewerbungen'] = array('Bewerbungen', 'Anzahl der Bewerbungen');
$GLOBALS['TL_LANG']['tl_fernschach_konten']['zusagen'] = array('Status', 'Anzahl der Zu- oder Absagen');

$GLOBALS['TL_LANG']['tl_fernschach_konten']['type_options'] = array
(
	'bank'    => 'Bank',
	'bar'     => 'Bargeld',
	'aktiva'  => 'Aktiva',
	'kredit'  => 'Kreditkarte',
	'fremd'   => 'Fremdkapital',
	'aktien'  => 'Aktienkonto',
	'invest'  => 'Investmentfonds',
	'ertrag'  => 'Ertrag',
	'aufwand' => 'Aufwand',
	'eigen'   => 'Eigenkapital',
	'fordern' => 'Offene Forderungen',
	'verbind' => 'Offene Verbindlichkeiten',
	'devisen' => 'Devisenhandel',
);
