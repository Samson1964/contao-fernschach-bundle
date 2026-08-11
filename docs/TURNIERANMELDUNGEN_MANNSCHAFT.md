# Technischer Ablauf bei Anmeldungen zu Mannschaftsturnieren

Hier wird dargestellt wie der interne Programmablauf bei Anmeldungen zu Mannschaftsturnieren ist.

## Aufbau des Anmeldeformulars

Das Formular wird in einem geschützten Bereich der Website angezeigt, der nur für BdF-Mitglieder zugänglich ist. Die Mitgliedsdaten stehen dadurch bereits zur Verfügung und müssen im Formular nicht abgefragt werden. Das Mitglied ist zugleich Mannschaftsführer der zu meldenden Mannschaft.

### Voraussetzungen einer Turnieranmeldung für den Mannschaftsführer

* Das SEPA-Mandat Beitrag liegt vor oder das Beitragskonto ist nicht im Minus. Trifft beides nicht zu, ist keine Anmeldung möglich.
* Das SEPA-Mandat Nenngeld liegt vor oder das Nenngeldkonto ist nicht im Minus. Im ersten Fall stehen alle Turniere zur Auswahl. Im zweiten Fall stehen nur Turniere zur Auswahl, wo das Nenngeld das Guthaben auf dem Nenngeldkonto nicht übersteigt.

### Voraussetzungen für Spieler der zu meldenden Mannschaft

* Das SEPA-Mandat Beitrag liegt vor oder das Beitragskonto ist nicht im Minus. Trifft beides nicht zu, ist keine Anmeldung möglich.

## Verarbeitung des Anmeldeformulars

Das ist z.Z. nur sehr einfach gelöst, da es keine Kindtabelle tl_fernschach_turniere_mannschaften. Es geht nur eine E-Mail an den Turnierleiter raus. Ziel ist es aber, die gemeldeten Mannschaften in so einer Kindtabelle zu speichern: Mannschaftsführer sowie alle x Spieler der Mannschaft (die Spieler ggfs. in einer weiteren Kindtabelle). Gleichzeitig wird für jeden Spieler ein Nenngeld-Datensatz mit 0 EUR erzeugt.
