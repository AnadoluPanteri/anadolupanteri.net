<?php
/**
 * Translate class.
 */
class Translate
{
	/**
	 * t function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function t($key){
		return $this->{$key};
	}

	/**
	 * c function.
	 *
	 * @access public
	 * @param mixed $key
	 * @param mixed $spinf
	 * @param mixed $change
	 * @return void
	 */
	public function c($key,$spinf,$change){
		return str_replace($spinf,$change,$this->t($key));
	}
}
?>
