<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021-2026 Frank Hoppe
 *
 * @package   Fernschach-Verwaltung
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2021-2026
 */

namespace Schachbulle\ContaoFernschachBundle\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Environment;
use Contao\FormCaptcha;
use Contao\Input;
use Contao\Module;
use Schachbulle\ContaoFernschachBundle\Classes\Beitritt;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

/**
 * Zeigt die Beitrittserklärung zum BdF und nimmt sie entgegen.
 *
 * Das Formular kam bisher aus einem von Hand gebauten Contao-Formular; das
 * Bundle hat sich die Werte nur über einen Hook abgeholt. Wer die Erweiterung
 * neu installierte, musste neunzehn Felder nachbauen, und ein umbenanntes Feld
 * fiel niemandem auf — der Datensatz entstand trotzdem, nur ohne diesen Wert.
 * Seit Version 2.13.0 bringt das Modul Felder, Prüfung und Ausgabe selbst mit.
 *
 * Die Felder stehen in @see \Schachbulle\ContaoFernschachBundle\Classes\Beitritt,
 * das Aussehen in fernschach_formular.css — dasselbe wie bei den beiden
 * Meldeformularen. Vom Theme wird nichts übernommen.
 */
class Beitrittserklaerung extends Module
{
	protected $strTemplate = 'mod_fernschach_beitritt';

	/**
	 * Zeigt im Backend nur einen Platzhalter statt des Formulars.
	 *
	 * @return string Die Ausgabe des Moduls
	 */
	public function generate()
	{
		if (Scope::isBackendRequest())
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### FERNSCHACH BEITRITTSERKLÄRUNG ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	/**
	 * Füllt das Template und verarbeitet eine Absendung.
	 *
	 * Nach dem Absenden wird auf dieselbe Seite mit `send=1` umgeleitet und dort
	 * die Bestätigung gezeigt. Ohne diese Umleitung landet der Absender wieder
	 * im ausgefüllten Formular, und ein Neuladen der Seite schickt den Antrag
	 * ein zweites Mal ab — genau das war beim Meldeformular jahrelang die
	 * Ursache für Mehrfachmeldungen.
	 *
	 * @return void Die Ausgabe entsteht über $this->Template
	 */
	protected function compile()
	{
		$this->Template->felder = Beitritt::felder();
		$this->Template->monate = Beitritt::monate();
		$this->Template->werte = array();
		$this->Template->fehler = array();
		$this->Template->fehlertext = '';
		$this->Template->bestaetigung = false;
		$this->Template->requestToken = Scope::getRequestToken();

		// Zieladresse des Formulars: dieselbe Seite, aber ohne den Dateinamen
		// index.php — genauso macht es das Formular des Contao-Kerns
		$this->Template->action = Environment::get("indexFreeRequest");

		// Eigene Gestaltung einbinden; ein Skript braucht das Formular nicht
		$GLOBALS['TL_CSS']['fernschach_formular'] = 'bundles/contaofernschach/css/fernschach_formular.css';

		// Rückkehr von der Umleitung nach dem Absenden
		if (Input::get('send'))
		{
			$this->Template->bestaetigung = true;

			return;
		}

		$objCaptcha = new FormCaptcha(array
		(
			'id'        => 'fs_captcha',
			'name'      => 'fs_captcha',
			'label'     => 'Ihre Antwort',
			'mandatory' => true,
		));

		$this->Template->captcha = $objCaptcha;

		if ('fs_beitritt' !== Input::post('FORM_SUBMIT'))
		{
			return;
		}

		// Werte einsammeln
		$arrWerte = array();

		foreach (Beitritt::felder() as $strName => $arrFeld)
		{
			$arrWerte[$strName] = trim((string) Input::post($strName));
		}

		$this->Template->werte = $arrWerte;

		$arrFehler = Beitritt::pruefe($arrWerte);

		// Die Sicherheitsfrage prüft sich selbst und meldet ihren Fehler am
		// Widget; abgeschickt wird trotzdem nichts
		$objCaptcha->validate();

		if ($objCaptcha->hasErrors())
		{
			$arrFehler['fs_captcha'] = 'Die Sicherheitsfrage wurde nicht richtig beantwortet.';
		}

		if ($arrFehler)
		{
			$this->Template->fehler = $arrFehler;
			$this->Template->fehlertext = 1 === \count($arrFehler)
				? 'Eine Angabe fehlt noch oder ist nicht in Ordnung. Sie steht unten rot hervorgehoben.'
				: \count($arrFehler).' Angaben fehlen noch oder sind nicht in Ordnung. Sie stehen unten rot hervorgehoben.';

			return;
		}

		$arrDoppelt = Beitritt::doppelgaenger($arrWerte);
		$intId = Beitritt::speichere($arrWerte);

		if (!$intId)
		{
			$this->Template->fehlertext = 'Ihr Antrag ließ sich nicht speichern. Bitte versuchen Sie es später noch einmal oder wenden Sie sich an die Geschäftsstelle.';

			return;
		}

		Beitritt::meldeSchatzmeister($intId, $arrWerte, $arrDoppelt);
		Beitritt::bestaetigeAntragsteller($arrWerte);

		Controller::redirect(Controller::addToUrl('send=1', true, array('send')));
	}
}
