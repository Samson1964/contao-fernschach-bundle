<?php

namespace Schachbulle\ContaoFernschachBundle\Classes\Accounts;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Input;

/**
 * Class Init
  */
class Init extends Backend
{

	function __construct()
	{
	}

	/**
	 * Erstellt einen Standardkontorahmen, aber nur wenn es noch keine Konten gibt
	 */
	public function run()
	{

		if(Input::get('key') != 'initAccounts')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import(BackendUser::class,'User');

	}

}
