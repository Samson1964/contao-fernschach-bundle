<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Accounts;

/**
 * Class Init
  */
class Init extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Erstellt einen Standardkontorahmen, aber nur wenn es noch keine Konten gibt
	 */
	public function run()
	{

		if(\Input::get('key') != 'initAccounts')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');

	}

}
