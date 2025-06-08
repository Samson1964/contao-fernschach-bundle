<?php

/**
 * Legenden
 */
$GLOBALS['TL_LANG']['tl_settings']['fernschach_legend']       = 'Fernschach-Verwaltung';

/**
 * Felder
 */
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetActive'] = array('Globalen Reset-Buchungsdatensatz aktivieren','Aktiviert einen Reset-Buchungsdatensatz bei jedem Spieler. Ist diese Option nicht aktiviert, werden evtl. vorhandene Reset-Buchungsdatensätze beim Aufruf der Buchungen eines Spielers gelöscht.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetRecords'] = array('Reset-Buchungsdatensätze','Sie können beliebig viele Reset-Buchungsdatensätze anlegen. Sie gelten in den ausgewählten Konten immer für alle Spieler.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetNumber'] = array('Nummer','Nummer des Reset-Buchungsdatensatzes.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetDate'] = array('Buchungsdatum','Datum und Uhrzeit des Reset-Buchungsdatensatzes.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetSaldo'] = array('Saldo','Betrag für den Reset-Buchungsdatensatz.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetKonten'] = array('Konten','Konten auswählen, für die der Reset-Buchungsdatensatz gelten soll. Mehrere Konten auswählen: STRG-Taste gedrückt halten beim Anklicken.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetUpdate'] = array('Letztes Update Kontostand','Datum und Uhrzeit der letzten Berechnung des Kontostandes in der Spielerliste. Die erneute Aktualisierung erfolgt, wenn der Zeitpunkt länger als 24 Stunden zurückliegt. Durch Ändern des Zeitpunktes können Sie die Aktualisierung beeinflussen.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetUpdate_time'] = array('Updaterhythmus in Sekunden','Die nächste Aktualisierung soll in dieser Anzahl Sekunden erfolgen. Die Voreinstellung ist 86400 (1 Tag).');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_maintenanceUpdate'] = array('Letztes Update Wartung','Datum und Uhrzeit der letzten Wartung der Spielerliste. Die erneute Aktualisierung erfolgt, wenn der Zeitpunkt länger als 12 Stunden zurückliegt. Durch Ändern des Zeitpunktes können Sie die Aktualisierung beeinflussen.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_maintenanceUpdate_time'] = array('Updaterhythmus in Sekunden','Die nächste Aktualisierung soll in dieser Anzahl Sekunden erfolgen. Die Voreinstellung ist 43200 (12 Stunden).');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_memberDefault'] = array('Standard-Mitgliedergruppe','Mitgliedergruppe, die normalen Mitgliederkonten zugeordnet werden soll (kein Mitglied im BdF). Leerlassen, wenn das nicht gewünscht ist.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_memberFernschach'] = array('BdF-Mitgliedergruppe','Mitgliedergruppe, die Mitgliederkonten von BdF-Mitgliedern zugeordnet werden soll. Leerlassen, wenn das nicht gewünscht ist.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_newsletter'] = array('Serienmail-Verteiler', 'Newsletter-Archiv auswählen, dessen Empfänger für Serienmails verwendet werden sollen.');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_emailVon'] = array('E-Mail-Absender','Globaler E-Mail-Absendername, z.B. Deutscher Fernschachbund');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_emailAdresse'] = array('E-Mail-Adresse','Globale E-Mail-Absenderadresse, z.B. info@bdf-fernschachbund.de');
$GLOBALS['TL_LANG']['tl_settings']['fernschach_hinweis_kontoauszug'] = array('Hinweis Kontoauszug','Hinweistext statt der Kontoauszug-Ausgabe, wenn Benutzer kein BdF-Mitglied ist.');

$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetKontenOptions'] = array
(
	'h' => 'Hauptkonto',
	'b' => 'Beitragskonto',
	'n' => 'Nenngeldkonto'
);
