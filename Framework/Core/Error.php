<?php
/**
 * Error class.
 * 
 * @extends Exception
 */
class Error extends Exception
{
	public function checkClass($class){
		if (!class_exists($class)) {
			if(APP_DEBUGING){
				print $this->show("Class not exist: ".$class);	
			}else{
				die();
			}
			return false;
		}
		return true;
	}
	
	
	public function show($msg){
		die("<b style='color:red;'>".$msg."</b>");
	}
}
?>
