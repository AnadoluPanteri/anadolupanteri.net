<?php
class Helpers
{
	public static function load($helperClass){
		require_once 'Framework/Helpers/'.$helperClass.'.php';
		return new $helperClass();
	}
	public static function inc($helperClass){
		require_once 'Framework/Helpers/'.$helperClass.'.php';
	}

	public static function ext($ext){
		require_once 'Framework/External/'.$ext.'.php';
	}
}
?>
