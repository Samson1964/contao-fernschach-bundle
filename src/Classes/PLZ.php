<?php

namespace Schachbulle\ContaoFernschachBundle\Classes;

class PLZ extends \Backend
{

	public $plz = array();
	
	public function __construct()
	{
	}

	/**
	 * Postleitzahl (PLZ) einem Bundesland zuordnen
	 *
	 * @param $string Postleitzahl
	 *
	 * @return array
	 */
	public static function get($plz)
	{
	}

	/**
	 * Ermittelt aus der PLZ das Bundesland, wenn das Bundesland nicht ausgewählt wurde
	 * @param mixed
	 * @param \DataContainer
	 * @return string
	 * @throws \Exception
	 */
	public function setBundesland(\DataContainer $dc)
	{
		$plzliste = self::plzliste();

		// 1. Bundesland festlegen
		$bundesland = $dc->activeRecord->bundesland;
		if(!$bundesland)
		{
			$plz = (int)$dc->activeRecord->plz;
			foreach($plzliste as $item)
			{
				if($plz >= $item[0] && $plz <= $item[1])
				{
					$bundesland = trim($item[2]);
					break;
				}
			}
			if($bundesland)
			{
				\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler SET bundesland = ? WHERE id = ?")
				                        ->execute($bundesland, $dc->id);
			}
		}

		// 2. Bundesland festlegen
		$bundesland = $dc->activeRecord->bundesland2;
		if(!$bundesland)
		{
			$plz = (int)$dc->activeRecord->plz2;
			foreach($plzliste as $item)
			{
				if($plz >= $item[0] && $plz <= $item[1])
				{
					$bundesland = trim($item[2]);
					break;
				}
			}
			if($bundesland)
			{
				\Database::getInstance()->prepare("UPDATE tl_fernschach_spieler SET bundesland2 = ? WHERE id = ?")
				                        ->execute($bundesland, $dc->id);
			}
		}
	}

	public static function plzliste()
	{
		$plz = array
		(
			array(1001, 1936, 'Sachsen'),
			array(1941, 1998, 'Brandenburg'),
			array(2601, 2999, 'Sachsen'),
			array(3001, 3253, 'Brandenburg'),
			array(4001, 4579, 'Sachsen'),
			array(4581, 4639, 'Thüringen'),
			array(4641, 4889, 'Sachsen'),
			array(4891, 4938, 'Brandenburg'),
			array(6001, 6548, 'Sachsen-Anhalt'),
			array(6551, 6578, 'Thüringen'),
			array(6601, 6928, 'Sachsen-Anhalt'),
			array(7301, 7919, 'Thüringen'),
			array(7919, 7919, 'Sachsen'),
			array(7919, 7919, 'Thüringen'),
			array(7919, 7919, 'Sachsen'),
			array(7920, 7950, 'Thüringen'),
			array(7951, 7951, 'Sachsen'),
			array(7952, 7952, 'Thüringen'),
			array(7952, 7952, 'Sachsen'),
			array(7953, 7980, 'Thüringen'),
			array(7982, 7982, 'Sachsen'),
			array(7985, 7985, 'Thüringen'),
			array(7985, 7985, 'Sachsen'),
			array(7985, 7989, 'Thüringen'),
			array(8001, 9669, 'Sachsen'),
			array(10001, 14330, 'Berlin'),
			array(14401, 14715, 'Brandenburg'),
			array(14715, 14715, 'Sachsen-Anhalt'),
			array(14723, 16949, 'Brandenburg'),
			array(17001, 17256, 'Mecklenburg-Vorpommern'),
			array(17258, 17258, 'Brandenburg'),
			array(17258, 17259, 'Mecklenburg-Vorpommern'),
			array(17261, 17291, 'Brandenburg'),
			array(17301, 17309, 'Mecklenburg-Vorpommern'),
			array(17309, 17309, 'Brandenburg'),
			array(17309, 17321, 'Mecklenburg-Vorpommern'),
			array(17321, 17321, 'Brandenburg'),
			array(17321, 17322, 'Mecklenburg-Vorpommern'),
			array(17326, 17326, 'Brandenburg'),
			array(17328, 17331, 'Mecklenburg-Vorpommern'),
			array(17335, 17335, 'Brandenburg'),
			array(17335, 17335, 'Mecklenburg-Vorpommern'),
			array(17337, 17337, 'Brandenburg'),
			array(17337, 19260, 'Mecklenburg-Vorpommern'),
			array(19271, 19273, 'Niedersachsen'),
			array(19273, 19273, 'Mecklenburg-Vorpommern'),
			array(19273, 19306, 'Mecklenburg-Vorpommern'),
			array(19307, 19357, 'Brandenburg'),
			array(19357, 19417, 'Mecklenburg-Vorpommern'),
			array(20001, 21037, 'Hamburg'),
			array(21039, 21039, 'Schleswig-Holstein'),
			array(21039, 21170, 'Hamburg'),
			array(21202, 21449, 'Niedersachsen'),
			array(21451, 21521, 'Schleswig-Holstein'),
			array(21522, 21522, 'Niedersachsen'),
			array(21524, 21529, 'Schleswig-Holstein'),
			array(21601, 21789, 'Niedersachsen'),
			array(22001, 22113, 'Hamburg'),
			array(22113, 22113, 'Schleswig-Holstein'),
			array(22115, 22143, 'Hamburg'),
			array(22145, 22145, 'Schleswig-Holstein'),
			array(22145, 22145, 'Hamburg'),
			array(22145, 22145, 'Schleswig-Holstein'),
			array(22147, 22786, 'Hamburg'),
			array(22801, 23919, 'Schleswig-Holstein'),
			array(23921, 23999, 'Mecklenburg-Vorpommern'),
			array(24001, 25999, 'Schleswig-Holstein'),
			array(26001, 27478, 'Niedersachsen'),
			array(27483, 27498, 'Schleswig-Holstein'),
			array(27499, 27499, 'Hamburg'),
			array(27501, 27580, 'Bremen'),
			array(27607, 27809, 'Niedersachsen'),
			array(28001, 28779, 'Bremen'),
			array(28784, 29399, 'Niedersachsen'),
			array(29401, 29416, 'Sachsen-Anhalt'),
			array(29431, 31868, 'Niedersachsen'),
			array(32001, 33829, 'Nordrhein-Westfalen'),
			array(34001, 34329, 'Hessen'),
			array(34331, 34353, 'Niedersachsen'),
			array(34355, 34355, 'Hessen'),
			array(34355, 34355, 'Niedersachsen'),
			array(34356, 34399, 'Hessen'),
			array(34401, 34439, 'Nordrhein-Westfalen'),
			array(34441, 36399, 'Hessen'),
			array(36401, 36469, 'Thüringen'),
			array(37001, 37194, 'Niedersachsen'),
			array(37194, 37195, 'Hessen'),
			array(37197, 37199, 'Niedersachsen'),
			array(37201, 37299, 'Hessen'),
			array(37301, 37359, 'Thüringen'),
			array(37401, 37649, 'Niedersachsen'),
			array(37651, 37688, 'Nordrhein-Westfalen'),
			array(37689, 37691, 'Niedersachsen'),
			array(37692, 37696, 'Nordrhein-Westfalen'),
			array(37697, 38479, 'Niedersachsen'),
			array(38481, 38489, 'Sachsen-Anhalt'),
			array(38501, 38729, 'Niedersachsen'),
			array(38801, 39649, 'Sachsen-Anhalt'),
			array(40001, 48432, 'Nordrhein-Westfalen'),
			array(48442, 48465, 'Niedersachsen'),
			array(48466, 48477, 'Nordrhein-Westfalen'),
			array(48478, 48480, 'Niedersachsen'),
			array(48481, 48485, 'Nordrhein-Westfalen'),
			array(48486, 48488, 'Niedersachsen'),
			array(48489, 48496, 'Nordrhein-Westfalen'),
			array(48497, 48531, 'Niedersachsen'),
			array(48541, 48739, 'Nordrhein-Westfalen'),
			array(49001, 49459, 'Niedersachsen'),
			array(49461, 49549, 'Nordrhein-Westfalen'),
			array(49551, 49849, 'Niedersachsen'),
			array(50101, 51597, 'Nordrhein-Westfalen'),
			array(51598, 51598, 'Rheinland-Pfalz'),
			array(51601, 53359, 'Nordrhein-Westfalen'),
			array(53401, 53579, 'Rheinland-Pfalz'),
			array(53581, 53604, 'Nordrhein-Westfalen'),
			array(53614, 53619, 'Rheinland-Pfalz'),
			array(53621, 53949, 'Nordrhein-Westfalen'),
			array(54181, 55239, 'Rheinland-Pfalz'),
			array(55240, 55252, 'Hessen'),
			array(55253, 56869, 'Rheinland-Pfalz'),
			array(57001, 57489, 'Nordrhein-Westfalen'),
			array(57501, 57648, 'Rheinland-Pfalz'),
			array(58001, 59966, 'Nordrhein-Westfalen'),
			array(59969, 59969, 'Hessen'),
			array(59969, 59969, 'Nordrhein-Westfalen'),
			array(60001, 63699, 'Hessen'),
			array(63701, 63774, 'Bayern'),
			array(63776, 63776, 'Hessen'),
			array(63776, 63928, 'Bayern'),
			array(63928, 63928, 'Baden-Württemberg'),
			array(63930, 63939, 'Bayern'),
			array(64201, 64753, 'Hessen'),
			array(64754, 64754, 'Baden-Württemberg'),
			array(64754, 65326, 'Hessen'),
			array(65326, 65326, 'Rheinland-Pfalz'),
			array(65327, 65391, 'Hessen'),
			array(65391, 65391, 'Rheinland-Pfalz'),
			array(65392, 65556, 'Hessen'),
			array(65558, 65582, 'Rheinland-Pfalz'),
			array(65583, 65620, 'Hessen'),
			array(65621, 65626, 'Rheinland-Pfalz'),
			array(65627, 65627, 'Hessen'),
			array(65629, 65629, 'Rheinland-Pfalz'),
			array(65701, 65936, 'Hessen'),
			array(66001, 66459, 'Saarland'),
			array(66461, 66509, 'Rheinland-Pfalz'),
			array(66511, 66839, 'Saarland'),
			array(66841, 67829, 'Rheinland-Pfalz'),
			array(68001, 68312, 'Baden-Württemberg'),
			array(68501, 68519, 'Hessen'),
			array(68520, 68549, 'Baden-Württemberg'),
			array(68601, 68649, 'Hessen'),
			array(68701, 69234, 'Baden-Württemberg'),
			array(69235, 69239, 'Hessen'),
			array(69240, 69429, 'Baden-Württemberg'),
			array(69430, 69431, 'Hessen'),
			array(69434, 69434, 'Baden-Württemberg'),
			array(69434, 69434, 'Hessen'),
			array(69435, 69469, 'Baden-Württemberg'),
			array(69479, 69488, 'Hessen'),
			array(69489, 69502, 'Baden-Württemberg'),
			array(69503, 69509, 'Hessen'),
			array(69510, 69514, 'Baden-Württemberg'),
			array(69515, 69518, 'Hessen'),
			array(70001, 74592, 'Baden-Württemberg'),
			array(74594, 74594, 'Bayern'),
			array(74594, 76709, 'Baden-Württemberg'),
			array(76711, 76891, 'Rheinland-Pfalz'),
			array(77601, 79879, 'Baden-Württemberg'),
			array(80001, 87490, 'Bayern'),
			array(87491, 87491, 'Außerhalb der BRD'),
			array(87493, 87561, 'Bayern'),
			array(87567, 87569, 'Außerhalb der BRD'),
			array(87571, 87789, 'Bayern'),
			array(88001, 88099, 'Baden-Württemberg'),
			array(88101, 88146, 'Bayern'),
			array(88147, 88147, 'Baden-Württemberg'),
			array(88147, 88179, 'Bayern'),
			array(88181, 89079, 'Baden-Württemberg'),
			array(89081, 89081, 'Bayern'),
			array(89081, 89085, 'Baden-Württemberg'),
			array(89087, 89087, 'Bayern'),
			array(89090, 89198, 'Baden-Württemberg'),
			array(89201, 89449, 'Bayern'),
			array(89501, 89619, 'Baden-Württemberg'),
			array(90001, 96489, 'Bayern'),
			array(96501, 96529, 'Thüringen'),
			array(97001, 97859, 'Bayern'),
			array(97861, 97877, 'Baden-Württemberg'),
			array(97888, 97892, 'Bayern'),
			array(97893, 97896, 'Baden-Württemberg'),
			array(97896, 97896, 'Bayern'),
			array(97897, 97900, 'Baden-Württemberg'),
			array(97901, 97909, 'Bayern'),
			array(97911, 97999, 'Baden-Württemberg'),
			array(98501, 99998, 'Thüringen')
		);
		return $plz;
	}
}
