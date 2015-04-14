<?php
/**
 * ArraySort class.
 */
namespace Bluejacket;
class ArraySort
{
	/**
	 * date function.
	 *
	 * @access public
	 * @static
	 * @param mixed $x
	 * @param mixed $y
	 * @return void
	 */
	public static function date($x, $y) {
		if ($x==$y) {
			krsort($array);
		}else if($x!=$y) {
				$formule = strtotime($y["date"]) - strtotime($x["date"]);
			}
		return $formule;
	}

	/**
	 * array_sort function.
	 *
	 * @access public
	 * @static
	 * @param mixed $array
	 * @param mixed $key
	 * @return void
	 */
	public static function array_sort($array,$key){
		switch($key){
		case "date":
			usort($array, array("ArraySort", "date"));
			break;
		}
		return $array;
	}
}
?>
