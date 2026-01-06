document.addEventListener('DOMContentLoaded', function() {
	//const anzahlZeilen = 46883;
	let zeile = 0;
	let ausgabekopf = document.querySelector("#progressheader");
	let ausgabebox = document.querySelector("#progressbar div");
	let ausgabefortschritt = document.querySelector("#progressbar span");
	let ausgabetext = document.querySelector("#progresstext");
	
	function rufePHPAuf() {
		if(zeile < anzahlZeilen) {
			// AJAX-Request mit fetch API
			fetch('/bundles/contaofernschach/Import_ICCF_Rating.php?zeile='+zeile, 
			{
				method: 'GET', // Oder POST
				headers: { 
					'Content-Type': 'application/json' 
				}, // Falls nötig
				//body: JSON.stringify({ 
				//	aufruf: zaehler 
				//}) // Falls Daten gesendet werden
			})
			.then(response => response.json())
			.then(data => {
				console.log('Antwort vom Server:', data);
				//console.log(data.titel);
				ausgabekopf.textContent = data.titel;
				ausgabetext.textContent = data.zeile+' / '+data.gesamt;
				let prozent = Math.floor(100 * (data.zeile / data.gesamt));
				ausgabebox.style.width = prozent+'%';
				ausgabefortschritt.textContent = prozent+'%';
				if(prozent === 100)
				{
					ausgabekopf.textContent = 'Import beendet';
					ausgabebox.style.background = 'green';
					if(data.zeile > data.gesamt)
					{
						ausgabetext.textContent = data.gesamt+' / '+data.gesamt;
					}
				};
				zeile = data.zeile;
				rufePHPAuf(); // Nächster Aufruf
			})
		} else {
			console.log('Alle drei Aufrufe abgeschlossen.');
		}
	}
	
	// Startet den Prozess
	rufePHPAuf();
});
