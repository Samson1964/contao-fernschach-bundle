<?php
namespace Schachbulle\ContaoFernschachBundle\Models;

use Contao\Model;

/**
 * add properties for IDE support
 * 
 * @property string $hash
 */
class Spieler extends \Model
{
	protected static $strTable = 'tl_fernschach_spieler';
	//protected static $strTable = 'tl_fernschach_spieler';
	
	// if you have logic you need more often, you can implement it here
	public function setHash()
	{
		$this->hash = md5($this->id);
	}
}
