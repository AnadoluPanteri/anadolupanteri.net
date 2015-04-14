<?php
/**
 * Helpers class.
 */
namespace Bluejacket; 
class Helpers
{
	/**
	 * load function.
	 *
	 * @access public
	 * @static
	 * @param mixed $helperClass
	 * @return void
	 */
	public static function load($helperClass){
		require_once 'Framework/Helpers/'.$helperClass.'.php';
		return new $helperClass();
	}

	/**
	 * inc function.
	 *
	 * @access public
	 * @static
	 * @param mixed $helperClass
	 * @return void
	 */
	public static function inc($helperClass){
		require_once 'Framework/Helpers/'.$helperClass.'.php';
	}

	/**
	 * ext function.
	 *
	 * @access public
	 * @static
	 * @param mixed $ext
	 * @return void
	 */
	public static function ext($ext){
		require_once 'Framework/External/'.$ext.'.php';
	}
}
?>
