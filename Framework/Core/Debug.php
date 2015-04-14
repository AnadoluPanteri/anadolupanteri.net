<?php
/**
 * Debug class.
 */
namespace Bluejacket; 
class Debug
{
	/**
	 * isFunction function.
	 *
	 * @access public
	 * @param mixed $c
	 * @return void
	 */
	public function isFunction($c){
		try{
			if(is_array($c)){
				if(!method_exists($c[1],$c[0])){
					throw new \Exception("Function doesnt exists!");
					return false;
				}else{
					throw new \Exception("Function exists!");
					return true;
				}
			}else{
				if(!function_exists($c)){
					throw new \Exception("Function doesnt exists!");
					return false;
				}else{
					throw new \Exception("Function exists!");
					return true;
				}
			}
		}catch(\Exception $e){
			echo $e->getMessage();
		}
	}
}
?>
