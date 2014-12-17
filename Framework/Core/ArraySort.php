<?php
class ArraySort
{
  public static function date($x, $y) {
		if ($x==$y) {
			krsort($array);
		}else if($x!=$y) {
			$formul = strtotime($y["date"]) - strtotime($x["date"]);
		}
		return $formul;
	}

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
