<?php

/**
 * Sprachdatei der Tabelle tl_fernschach_konten_buchungen
 *
 * Bis Version 1.9.6 lag an dieser Stelle versehentlich eine Kopie der
 * DCA-Datei. Damit wurde beim Laden der Sprachdatei die Klasse
 * tl_fernschach_konten_buchungen ein zweites Mal deklariert und das schon
 * aufgebaute DCA überschrieben; zugleich fehlten sämtliche Beschriftungen. Die
 * Datei enthält jetzt das, was sie enthalten soll.
 *
 * Der gemeinsame Teil steht in konten.php und wird über die Variable $strTable
 * für die jeweilige Tabelle eingebunden — genauso wie bei den drei
 * Spielerkonten (siehe tl_fernschach_spieler_konto.php).
 */

$strTable = 'tl_fernschach_konten_buchungen';
include_once('konten.php');

/**
 * Spaltenüberschriften der Listenansicht
 *
 * Beide Felder gibt es nur in dieser Tabelle, deshalb stehen sie nicht in
 * konten.php.
 */
$GLOBALS['TL_LANG'][$strTable]['soll'] = array('Soll', 'Soll-Buchung (Forderung an das Konto)');
$GLOBALS['TL_LANG'][$strTable]['haben'] = array('Haben', 'Haben-Buchung (Zahlung auf das Konto)');
