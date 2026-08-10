/*
 * Verhalten des Mannschaftsmeldeformulars der Fernschach-Verwaltung.
 *
 * Das Skript kommt ohne Bibliothek aus — weder MooTools noch jQuery, die je nach
 * Theme vorhanden sind oder eben nicht. Es übernimmt drei Aufgaben:
 *
 *   1. Es baut so viele Bretter auf, wie das gewählte Turnier zulässt.
 *   2. Es schlägt ab dem zweiten Zeichen passende Spieler vor; gesucht werden
 *      Name, BdF-Mitgliedsnummer und ICCF-ID.
 *   3. Es prüft vor dem Abschicken, ob alle Pflichtfelder gefüllt sind und kein
 *      Spieler doppelt aufgestellt wurde.
 *
 * Die Prüfung im Browser ist nur die schnelle Rückmeldung. Verlassen wird sich
 * darauf nicht: Das Modul prüft jede Meldung serverseitig noch einmal komplett.
 */
(function () {
	'use strict';

	/** Ab wie vielen Zeichen die Suche losgeschickt wird (wie im Controller). */
	var MINDESTLAENGE = 2;

	/** Wartezeit in Millisekunden, bevor getippter Text zur Suche wird. */
	var TIPPPAUSE = 220;

	/**
	 * Maskiert Text für die Ausgabe als HTML.
	 *
	 * Die Vorschläge stammen aus der Datenbank und damit letztlich aus
	 * Benutzereingaben; ohne Maskierung ließe sich über einen Spielernamen
	 * fremdes Markup in die Seite bringen.
	 *
	 * @param {string} text Der auszugebende Text, darf auch undefined sein
	 *
	 * @return {string} Der Text mit maskierten Sonderzeichen, leer bei undefined
	 */
	function maskiere(text) {
		var div = document.createElement('div');
		div.textContent = text == null ? '' : String(text);

		return div.innerHTML;
	}

	/**
	 * Setzt oder entfernt die Fehlermarkierung eines Feldes.
	 *
	 * Die Meldung wird als eigener Absatz unter das Feld gehängt und beim
	 * nächsten Aufruf wiederverwendet, damit sich bei mehrfacher Prüfung keine
	 * Absätze stapeln.
	 *
	 * @param {Element}     feld    Der umschließende Block mit der Klasse fs-feld
	 * @param {string|null} meldung Der Fehlertext, oder null bzw. leer zum Aufheben
	 *
	 * @return {void} Verändert ausschließlich das übergebene Element
	 */
	function markiere(feld, meldung) {
		if (!feld) {
			return;
		}

		var absatz = feld.querySelector('.fs-fehler');

		if (!meldung) {
			feld.classList.remove('fs-feld--fehler');

			if (absatz) {
				absatz.remove();
			}

			return;
		}

		feld.classList.add('fs-feld--fehler');

		if (!absatz) {
			absatz = document.createElement('p');
			absatz.className = 'fs-fehler';
			feld.appendChild(absatz);
		}

		absatz.textContent = meldung;
	}

	/**
	 * Richtet die Autovervollständigung für ein einzelnes Bretteingabefeld ein.
	 *
	 * Sichtbar ist ein gewöhnliches Textfeld; die eigentliche Spieler-ID steht in
	 * einem versteckten Feld daneben und wird nur durch Auswahl eines Vorschlags
	 * gesetzt. Tippt der Benutzer danach weiter, gilt die Auswahl als verworfen —
	 * so lässt sich kein Name absenden, hinter dem keine gültige ID steht.
	 *
	 * @param {HTMLInputElement} eingabe    Das sichtbare Textfeld
	 * @param {HTMLInputElement} versteckt  Das versteckte Feld mit der Spieler-ID
	 * @param {HTMLElement}      liste      Die Liste, in die Vorschläge kommen
	 * @param {string}           suchadresse Adresse der Suchroute des Bundles
	 *
	 * @return {void} Hängt Ereignisbehandlungen an die übergebenen Elemente
	 */
	function verknuepfeSuche(eingabe, versteckt, liste, suchadresse) {
		var zeitgeber = null;
		var laufend = null;
		var treffer = [];
		var markiert = -1;

		/**
		 * Schließt die Vorschlagsliste und setzt die Tastaturauswahl zurück.
		 *
		 * @return {void}
		 */
		function schliesse() {
			liste.hidden = true;
			liste.innerHTML = '';
			treffer = [];
			markiert = -1;
			eingabe.setAttribute('aria-expanded', 'false');
			eingabe.removeAttribute('aria-activedescendant');
		}

		/**
		 * Hebt den Eintrag mit dem angegebenen Index hervor und scrollt ihn in Sicht.
		 *
		 * @param {number} index Position in der Trefferliste, außerhalb = keine Auswahl
		 *
		 * @return {void}
		 */
		function hebeHervor(index) {
			var eintraege = liste.querySelectorAll('li');
			var i;

			for (i = 0; i < eintraege.length; i++) {
				eintraege[i].setAttribute('aria-selected', i === index ? 'true' : 'false');
			}

			markiert = index;

			if (index >= 0 && eintraege[index]) {
				eingabe.setAttribute('aria-activedescendant', eintraege[index].id);

				var oben = eintraege[index].offsetTop;
				var unten = oben + eintraege[index].offsetHeight;

				if (oben < liste.scrollTop) {
					liste.scrollTop = oben;
				} else if (unten > liste.scrollTop + liste.clientHeight) {
					liste.scrollTop = unten - liste.clientHeight;
				}
			} else {
				eingabe.removeAttribute('aria-activedescendant');
			}
		}

		/**
		 * Übernimmt einen Vorschlag in das Feld.
		 *
		 * @param {number} index Position in der Trefferliste
		 *
		 * @return {void} Setzt sichtbaren Text und versteckte ID, schließt die Liste
		 */
		function uebernimm(index) {
			if (!treffer[index]) {
				return;
			}

			eingabe.value = treffer[index].text;
			versteckt.value = treffer[index].id;
			markiere(eingabe.closest('.fs-feld'), null);
			schliesse();
		}

		/**
		 * Zeigt die Antwort der Suche als Vorschlagsliste an.
		 *
		 * @param {Array} daten Liste von Objekten mit id, text und info
		 *
		 * @return {void}
		 */
		function zeige(daten) {
			treffer = Array.isArray(daten) ? daten : [];

			if (!treffer.length) {
				liste.innerHTML = '<li class="fs-leer" role="presentation">Kein passender Spieler gefunden</li>';
				liste.hidden = false;
				eingabe.setAttribute('aria-expanded', 'true');
				markiert = -1;

				return;
			}

			var html = '';
			var i;

			for (i = 0; i < treffer.length; i++) {
				html += '<li id="' + liste.id + '-' + i + '" role="option" aria-selected="false" data-index="' + i + '">'
					+ maskiere(treffer[i].text)
					+ (treffer[i].info ? '<span class="fs-vorschlag-info">' + maskiere(treffer[i].info) + '</span>' : '')
					+ '</li>';
			}

			liste.innerHTML = html;
			liste.hidden = false;
			liste.scrollTop = 0;
			eingabe.setAttribute('aria-expanded', 'true');
			hebeHervor(-1);
		}

		/**
		 * Fragt die Vorschläge zum aktuellen Feldinhalt beim Server ab.
		 *
		 * Eine noch laufende Abfrage wird abgebrochen: Beim schnellen Tippen käme
		 * sonst womöglich die ältere Antwort später an und überschriebe die neuere.
		 *
		 * @return {void}
		 */
		function frage() {
			var suche = eingabe.value.trim();

			if (laufend) {
				laufend.abort();
				laufend = null;
			}

			if (suche.length < MINDESTLAENGE) {
				schliesse();

				return;
			}

			var abbruch = typeof AbortController === 'function' ? new AbortController() : null;
			laufend = abbruch;

			fetch(suchadresse + (suchadresse.indexOf('?') === -1 ? '?' : '&') + 'q=' + encodeURIComponent(suche), {
				credentials: 'same-origin',
				headers: {'X-Requested-With': 'XMLHttpRequest'},
				signal: abbruch ? abbruch.signal : undefined
			})
				.then(function (antwort) {
					if (!antwort.ok) {
						throw new Error('Status ' + antwort.status);
					}

					return antwort.json();
				})
				.then(function (daten) {
					laufend = null;
					zeige(daten);
				})
				.catch(function (fehler) {
					// Ein abgebrochener Aufruf ist kein Fehler, sondern der Normalfall
					// beim Weitertippen.
					if (fehler && fehler.name === 'AbortError') {
						return;
					}

					laufend = null;
					liste.innerHTML = '<li class="fs-leer" role="presentation">Die Spielersuche ist zurzeit nicht erreichbar.</li>';
					liste.hidden = false;
				});
		}

		eingabe.addEventListener('input', function () {
			// Jede Änderung verwirft die bisherige Auswahl — sonst bliebe die ID
			// eines Spielers stehen, dessen Name gar nicht mehr im Feld steht.
			versteckt.value = '';

			window.clearTimeout(zeitgeber);
			zeitgeber = window.setTimeout(frage, TIPPPAUSE);
		});

		eingabe.addEventListener('keydown', function (ereignis) {
			if (liste.hidden && ereignis.key === 'ArrowDown') {
				frage();

				return;
			}

			if (liste.hidden) {
				return;
			}

			if (ereignis.key === 'ArrowDown') {
				ereignis.preventDefault();
				hebeHervor(markiert + 1 >= treffer.length ? 0 : markiert + 1);
			} else if (ereignis.key === 'ArrowUp') {
				ereignis.preventDefault();
				hebeHervor(markiert - 1 < 0 ? treffer.length - 1 : markiert - 1);
			} else if (ereignis.key === 'Enter') {
				if (markiert >= 0) {
					// Nur wenn tatsächlich ein Vorschlag ausgewählt ist; sonst soll
					// die Eingabetaste das Formular abschicken dürfen.
					ereignis.preventDefault();
					uebernimm(markiert);
				}
			} else if (ereignis.key === 'Escape') {
				schliesse();
			}
		});

		// mousedown statt click: Bei click wäre das Feld schon unscharf und die
		// Liste über das blur-Ereignis bereits geschlossen.
		liste.addEventListener('mousedown', function (ereignis) {
			var eintrag = ereignis.target.closest('li[data-index]');

			if (!eintrag) {
				return;
			}

			ereignis.preventDefault();
			uebernimm(parseInt(eintrag.getAttribute('data-index'), 10));
		});

		eingabe.addEventListener('blur', function () {
			// Kurz verzögert, damit ein Klick auf einen Vorschlag noch ankommt.
			window.setTimeout(schliesse, 150);
		});
	}

	/**
	 * Richtet ein Mannschaftsmeldeformular vollständig ein.
	 *
	 * @param {HTMLFormElement} formular Das Formular mit data-fernschach-mannschaft
	 *
	 * @return {void} Ohne Turnierauswahl im Formular geschieht nichts
	 */
	function richteEin(formular) {
		var auswahl = formular.querySelector('[data-fs-turnier]');
		var block = formular.querySelector('[data-fs-bretter]');
		var behaelter = formular.querySelector('[data-fs-brettliste]');

		if (!auswahl || !block || !behaelter) {
			return;
		}

		var suchadresse = formular.getAttribute('data-suche') || '';
		var zaehler = 0;

		// Bereits abgeschickte Aufstellung, die nach einem Fehler wieder in die
		// Felder soll. Sie steht als JSON neben dem Formular.
		var vorgaben = {};
		var wurzel = formular.closest('.fernschach-formular') || document;
		var quelle = wurzel.querySelector('[data-fs-vorgaben]');

		if (quelle) {
			try {
				vorgaben = JSON.parse(quelle.textContent) || {};
			} catch (fehler) {
				vorgaben = {};
			}
		}

		/**
		 * Baut die Bretteingaben für das gerade gewählte Turnier auf.
		 *
		 * Schon eingetragene Spieler bleiben erhalten, soweit das neue Turnier
		 * genügend Bretter hat — wer sich in der Turnierauswahl vertan hat, muss
		 * seine Aufstellung nicht noch einmal eingeben.
		 *
		 * @return {void}
		 */
		function baueBretter() {
			var option = auswahl.options[auswahl.selectedIndex];
			var bretter = option ? parseInt(option.getAttribute('data-bretter'), 10) : 0;

			if (!bretter || isNaN(bretter)) {
				behaelter.innerHTML = '';
				block.hidden = true;

				return;
			}

			// Vorhandene Eingaben sichern, bevor der Behälter neu gefüllt wird
			var gesichert = {};
			var felder = behaelter.querySelectorAll('.fs-brett-zeile');
			var i;

			for (i = 0; i < felder.length; i++) {
				gesichert[i + 1] = {
					id: felder[i].querySelector('input[type="hidden"]').value,
					name: felder[i].querySelector('input[type="text"]').value
				};
			}

			behaelter.innerHTML = '';
			block.hidden = false;

			for (i = 1; i <= bretter; i++) {
				var wert = gesichert[i] || vorgaben[i] || {};
				var listenId = 'fs-vorschlaege-' + (++zaehler);
				var feldId = 'fs-spieler-' + zaehler;

				var zeile = document.createElement('div');
				zeile.className = 'fs-brett-zeile';
				zeile.innerHTML =
					'<div class="fs-brett-nummer"><label for="' + feldId + '">Brett ' + i + '</label></div>'
					+ '<div class="fs-brett-eingabe fs-feld">'
					+ '<input type="text" id="' + feldId + '" class="fs-spieler" autocomplete="off"'
					+ ' role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="' + listenId + '"'
					+ ' placeholder="Name, BdF-Nummer oder ICCF-ID">'
					+ '<input type="hidden" name="spieler_' + i + '">'
					+ '<ul class="fs-vorschlaege" id="' + listenId + '" role="listbox" hidden></ul>'
					+ '</div>';

				behaelter.appendChild(zeile);

				var eingabe = zeile.querySelector('input[type="text"]');
				var versteckt = zeile.querySelector('input[type="hidden"]');

				// Werte über die Eigenschaft setzen statt über das Attribut: Ein
				// Anführungszeichen im Namen würde sonst das Markup zerreißen.
				eingabe.value = wert.name || '';
				versteckt.value = wert.id || '';

				verknuepfeSuche(eingabe, versteckt, zeile.querySelector('.fs-vorschlaege'), suchadresse);

				// Serverseitige Meldung des letzten Versuchs anzeigen
				if (vorgaben[i] && vorgaben[i].fehler && !gesichert[i]) {
					markiere(eingabe.closest('.fs-feld'), vorgaben[i].fehler);
				}
			}
		}

		auswahl.addEventListener('change', baueBretter);
		baueBretter();

		formular.addEventListener('submit', function (ereignis) {
			var fehlerhaft = null;
			var i;

			/**
			 * Prüft ein Pflichtfeld auf Inhalt und merkt sich das erste leere.
			 *
			 * @param {Element} feld    Das Eingabe- oder Auswahlfeld
			 * @param {string}  meldung Der Text, der bei leerem Feld erscheint
			 *
			 * @return {boolean} true, wenn das Feld gefüllt ist
			 */
			function pflicht(feld, meldung) {
				if (!feld) {
					return true;
				}

				var block = feld.closest('.fs-feld');

				if (!feld.value.trim()) {
					markiere(block, meldung);
					fehlerhaft = fehlerhaft || feld;

					return false;
				}

				markiere(block, null);

				return true;
			}

			pflicht(auswahl, 'Bitte wählen Sie ein Turnier aus.');
			pflicht(formular.querySelector('[name="vereinsname"]'), 'Bitte geben Sie den Vereinsnamen an.');
			pflicht(formular.querySelector('[name="mannschaftsname"]'), 'Bitte geben Sie die Bezeichnung der Mannschaft an.');

			// Aufstellung: jedes Brett besetzt, und kein Spieler zweimal
			var zeilen = behaelter.querySelectorAll('.fs-brett-zeile');
			var vergeben = {};

			for (i = 0; i < zeilen.length; i++) {
				var eingabe = zeilen[i].querySelector('input[type="text"]');
				var versteckt = zeilen[i].querySelector('input[type="hidden"]');
				var feld = zeilen[i].querySelector('.fs-feld');

				if (!versteckt.value) {
					markiere(feld, eingabe.value.trim()
						? 'Bitte wählen Sie den Spieler aus der Vorschlagsliste aus.'
						: 'Bitte besetzen Sie Brett ' + (i + 1) + '.');
					fehlerhaft = fehlerhaft || eingabe;

					continue;
				}

				if (vergeben[versteckt.value]) {
					markiere(feld, 'Dieser Spieler steht bereits an Brett ' + vergeben[versteckt.value] + '.');
					fehlerhaft = fehlerhaft || eingabe;

					continue;
				}

				vergeben[versteckt.value] = i + 1;
				markiere(feld, null);
			}

			if (fehlerhaft) {
				ereignis.preventDefault();
				fehlerhaft.focus();

				return;
			}

			// Doppelklick auf den Knopf soll die Meldung nicht zweimal abschicken
			var knopf = formular.querySelector('button[type="submit"]');

			if (knopf) {
				window.setTimeout(function () {
					knopf.disabled = true;
					knopf.textContent = 'Wird gesendet …';
				}, 0);
			}
		});
	}

	/**
	 * Sucht alle Mannschaftsmeldeformulare der Seite und richtet sie ein.
	 *
	 * @return {void}
	 */
	function starte() {
		var formulare = document.querySelectorAll('[data-fernschach-mannschaft]');
		var i;

		for (i = 0; i < formulare.length; i++) {
			richteEin(formulare[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', starte);
	} else {
		starte();
	}
})();
