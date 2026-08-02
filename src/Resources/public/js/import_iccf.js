/*
 * Fortschrittsanzeige für den ICCF-Wertungslistenimport.
 *
 * Die Wertungsliste wird blockweise eingelesen: Das Skript ruft die Route
 * /contao/fernschach/iccf-import so lange auf, bis die zuletzt verarbeitete
 * Zeile die Gesamtzahl erreicht hat. Die Gesamtzahl steht in der globalen
 * Variablen anzahlZeilen, die Classes/ImportProgress vor dem Einbinden dieser
 * Datei ausgibt.
 *
 * Bis Version 1.9.6 zeigte der Aufruf auf die Datei
 * bundles/contaofernschach/Import_ICCF_Rating.php. Direkt aufrufbaren PHP-Code
 * im Asset-Verzeichnis gibt es seit der Portierung auf Contao 5 nicht mehr.
 */
document.addEventListener('DOMContentLoaded', function() {
	let zeile = 0;
	let ausgabekopf = document.querySelector("#progressheader");
	let ausgabebox = document.querySelector("#progressbar div");
	let ausgabefortschritt = document.querySelector("#progressbar span");
	let ausgabetext = document.querySelector("#progresstext");

	function rufePHPAuf() {
		if (zeile >= anzahlZeilen) {
			return;
		}

		fetch('/contao/fernschach/iccf-import?zeile=' + zeile, {
			method: 'GET',
			// Ohne das Mitschicken der Anmeldedaten weist Contao die Anfrage
			// im Backend-Scope zurück.
			credentials: 'same-origin',
			headers: {
				'Accept': 'application/json'
			}
		})
		.then(response => response.json())
		.then(data => {
			ausgabekopf.textContent = data.titel;

			// Ohne Gesamtzahl ist etwas schiefgegangen; data.titel enthält dann
			// den Grund und der Vorgang wird nicht fortgesetzt.
			if (!data.gesamt) {
				ausgabebox.style.background = 'red';
				return;
			}

			ausgabetext.textContent = data.zeile + ' / ' + data.gesamt;

			let prozent = Math.floor(100 * (data.zeile / data.gesamt));
			ausgabebox.style.width = prozent + '%';
			ausgabefortschritt.textContent = prozent + '%';

			if (prozent >= 100) {
				ausgabekopf.textContent = 'Import beendet';
				ausgabebox.style.background = 'green';
				ausgabetext.textContent = data.gesamt + ' / ' + data.gesamt;
			}

			// Bleibt der Zähler stehen, kommt der Import nicht voran — dann
			// lieber abbrechen als endlos weiterfragen.
			if (data.zeile <= zeile) {
				return;
			}

			zeile = data.zeile;
			rufePHPAuf();
		})
		.catch(fehler => {
			ausgabekopf.textContent = 'Import abgebrochen: ' + fehler;
			ausgabebox.style.background = 'red';
		});
	}

	rufePHPAuf();
});
