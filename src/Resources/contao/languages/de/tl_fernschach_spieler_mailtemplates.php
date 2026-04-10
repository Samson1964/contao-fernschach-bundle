<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 */

/**
 * Backend-Modul Übersetzungen
 */

// Standardfunktionen
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['new'] = array('Neues Template', 'Neues Template anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['editHeader'] = array("Template %s bearbeiten", "Template %s bearbeiten");
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['copy'] = array("Template %s kopieren", "Template %s kopieren");
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['delete'] = array("Template %s löschen", "Template %s löschen");
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['toggle'] = array("Template %s aktivieren/deaktivieren", "Template %s aktivieren/deaktivieren");
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['show'] = array("Details zum Template %s anzeigen", "Details zum Template %s anzeigen");

// Formularfelder
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['name_legend'] = 'Name und Inhalt';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['name']= array('Name', 'Name des Templates');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['description']= array('Beschreibung', 'Kurze Beschreibung des Templates');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['template']= array('HTML-Inhalt', 'Bitte den Inhalt des Templates mit HTML-Code schreiben. Verwenden Sie den Hilfe-Link für weitere Informationen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['publish_legend'] = 'Aktivierung';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['published']= array('Aktiv', 'Template aktivieren');

// Beispiel-Template
$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['default_template']= 
'{if content != ""}
	##content##
{endif}

{if signatur != ""}
	##signatur##
{else}
	<p>Ihr Deutscher Fernschachbund e.V.<br>
	<a href="https://www.bdf-fernschachbund.de/" target="_blank">www.bdf-fernschachbund.de</p>
{endif}';
