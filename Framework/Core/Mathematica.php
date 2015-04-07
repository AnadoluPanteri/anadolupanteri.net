<?php
/**
 * Mathematica class.
 */
class Mathematica{
	/**
	 * float function.
	 * 
	 * @access public
	 * @param mixed $num
	 * @return void
	 */
	function toFloat($num) {
	    $dotPos = strrpos($num, '.');
	    $commaPos = strrpos($num, ',');
	    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : 
	        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
	   
	    if (!$sep) {
	        return floatval(preg_replace("/[^0-9]/", "", $num));
	    } 
	
	    return floatval(
	        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
	        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
	    );
	}
}
?>