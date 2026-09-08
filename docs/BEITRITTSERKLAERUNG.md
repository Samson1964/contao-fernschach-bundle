# Beitrittserklärung zum BdF

Wer dem Deutschen Fernschachbund beitreten will, füllt im Frontend die
Beitrittserklärung aus. Daraus entsteht ein Spielerdatensatz, der Schatzmeister
wird benachrichtigt, und der Antragsteller bekommt eine Bestätigung.

## Einrichtung

Frontend-Modul **Beitrittserklärung** anlegen und auf einer Seite einbinden —
mehr ist nicht nötig. Das Modul bringt Felder, Prüfung, Aussehen und Versand
selbst mit; ein Contao-Formular wird dafür nicht gebraucht.

Das Aussehen kommt aus `fernschach_formular.css` und setzt sich gegen das Theme
durch. Nötig ist das, weil verbreitete Themes — auf bdf-fernschachbund.de etwa
Materialize — Ankreuz- und Auswahlfelder ausblenden und durch eigene Zeichnungen
ersetzen, die eine bestimmte Markup-Folge voraussetzen. Ohne diese Regeln wären
Ankreuzfelder unsichtbar und Auswahllisten gar nicht vorhanden.

Damit die Benachrichtigung ankommt, gehören in *System → Einstellungen*:

* **Name** und **E-Mail-Adresse des Schatzmeisters** — der Empfänger. Fehlt die
  Adresse, unterbleibt die Nachricht und es entsteht ein Eintrag im
  Systemprotokoll; der Antrag selbst wird trotzdem gespeichert.
* **E-Mail-Absender** und **E-Mail-Adresse** — der Absender beider Nachrichten.

## Die Felder

| Feld | Pflicht | Wohin es geht |
| --- | --- | --- |
| Vorname, Nachname | ja | `vorname`, `nachname` |
| Straße/Nr., PLZ, Wohnort | ja | `strasse`, `plz`, `ort` |
| Geburtsdatum | ja | `birthday`, umgerechnet in die Speicherform `JJJJMMTT` |
| Staatsangehörigkeit | ja | Fließtext *Informationen zum Beitritt* |
| Telefon | nein | `telefon1` |
| E-Mail | nein | `email1` |
| Bereits BdF-Mitglied gewesen? | ja | Fließtext |
| Wenn ja, Mitglieds-Nr. | nein | `memberId` — aber nur, wenn die Frage davor mit *Ja* beantwortet wurde |
| Bisherige Erfolge im Fern- und Nahschach | nein | Fließtext |
| Nahschach-Elo, Nahschach-DWZ | nein | Fließtext |
| Beitritt zum 1. des Monats | ja | Fließtext |
| Beitrittserklärung, Datenschutz | ja | Fließtext |

Die Liste steht in `Classes\Beitritt::felder()`. Ein neues Feld wird dort
nachgetragen; Ausgabe, Prüfung und E-Mails richten sich danach.

Der **Beitrittsmonat** ist eine Auswahl aus dem laufenden und den drei
folgenden Monaten, nicht ein Textfeld — ein von Hand eingetippter Monat ist
eine Fehlerquelle ohne Gegenwert.

## Was geprüft wird

Geprüft wird auf dem Server. Was ein Formular an Pflichtangaben verlangt, lässt
sich mit den Werkzeugen jedes Browsers in zehn Sekunden abschalten; deshalb
zählt allein, was hinten ankommt.

* Alle Pflichtfelder sind ausgefüllt, beide Häkchen gesetzt.
* Das **Geburtsdatum** gibt es wirklich (`31.02.` nicht), liegt nicht in der
  Zukunft und nicht mehr als 120 Jahre zurück.
* Eine angegebene **E-Mail-Adresse** sieht aus wie eine.
* **Elo** und **DWZ** sind, wenn angegeben, Zahlen zwischen 500 und 3500.
* **Auswahl- und Ankreuzfelder** nehmen nur an, was auch angeboten wurde.
* Eine **Sicherheitsfrage** (Rechenaufgabe des Contao-Kerns samt verstecktem
  Lockfeld) hält einfache Maschinen ab. Wer JavaScript eingeschaltet hat,
  bekommt sie nie zu sehen: Contao blendet sie dann selbst aus und trägt die
  Antwort ein. Deshalb steht sie ohne Überschrift und ohne Kasten da — sonst
  bliebe eine leere Umrandung stehen.

Beanstandungen stehen als Sammelmeldung über dem Formular und noch einmal
einzeln am jeweiligen Feld. Alle Eingaben bleiben stehen.

## Was nach dem Absenden geschieht

1. Der **Spielerdatensatz** entsteht — veröffentlicht, wie es der alte Weg über
   das Contao-Formular auch gemacht hat. Er ist damit noch keine
   Mitgliedschaft: Die Mitgliedschaftszeiträume trägt die Geschäftsstelle ein,
   und ohne sie zählt der Datensatz in keiner Auswertung als Mitglied.
2. Der **Schatzmeister** bekommt alle Angaben, einen Link auf den neuen
   Datensatz und, sofern eine Adresse angegeben wurde, den Antragsteller als
   Rückantwortadresse.
3. Gibt es bereits einen Datensatz mit **demselben Namen und Geburtsdatum**,
   steht das in der Nachricht. Der Antrag wird trotzdem angenommen — wer schon
   einmal Mitglied war, tritt zu Recht ein zweites Mal ein —, aber niemand soll
   zwei Datensätze nebeneinander weiterlaufen lassen, ohne es zu merken.
4. Der **Antragsteller** bekommt eine Eingangsbestätigung, sofern er eine
   E-Mail-Adresse angegeben hat.
5. Der Vorgang steht im **Systemprotokoll**.

Anschließend wird auf dieselbe Seite mit `send=1` umgeleitet und dort die
Bestätigung gezeigt. Ohne diese Umleitung landet der Absender wieder im
ausgefüllten Formular, und ein Neuladen der Seite schickt den Antrag ein
zweites Mal ab — genau das war beim Meldeformular jahrelang die Ursache für
Mehrfachmeldungen.

## Der alte Weg

Vor Version 2.13.0 war die Beitrittserklärung ein von Hand gebautes
Contao-Formular; das Bundle holte sich die Werte über den Hook
`processFormData` und legte den Datensatz an. Der Weg **funktioniert weiterhin**
— wer in *System → Einstellungen* ein Beitrittsformular hinterlegt hat, muss
nichts umstellen.

Neu einrichten sollte man ihn nicht mehr: Er verlangt, neunzehn Felder mit
genau den richtigen Feldnamen nachzubauen, und ein umbenanntes Feld fällt
niemandem auf — der Datensatz entsteht trotzdem, nur eben ohne diesen Wert.
Auch die Prüfungen, die Benachrichtigung des Schatzmeisters und die Bestätigung
gibt es dort nicht.

Wer umstellt, entfernt das Formular aus den Einstellungen und ersetzt es auf
der Seite durch das Frontend-Modul.
