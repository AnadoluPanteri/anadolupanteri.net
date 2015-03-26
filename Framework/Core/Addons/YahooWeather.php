<?php
/**
 * YahooWeather class.
 */
class YahooWeather
{
	/**
	 * code
	 *
	 * @var mixed
	 * @access public
	 */
	public $code;
	/**
	 * code function.
	 *
	 * @access public
	 * @param mixed $code
	 * @return void
	 */
	function code($code){
		$this->code = $code;
	}

	/**
	 * setCity function.
	 *
	 * @access public
	 * @param mixed $city
	 * @return void
	 */
	function setCity($city){
		foreach($this->trCodeList as $k => $v){
			if($city == strtolower($v)){
				$this->code($k);
			}
		}
	}

	/**
	 * getHtml function.
	 *
	 * @access public
	 * @return void
	 */
	function getHtml(){
		$doc = new \DOMDocument();
		$doc->load("http://weather.yahooapis.com/forecastrss?p=".$this->code."&u=c");

		$channel = $doc->getElementsByTagName("channel");

		$out = null;
		foreach($channel as $chnl){
			$item=$chnl->getElementsByTagName("item");
			foreach($item as $it){
				$describe = $it->getElementsByTagName("description");
				$description = $describe->item(0)->nodeValue;
				$out .= $description;

			}
		}

		$out = explode("<a href=",$out);
		$out = $out[0];
		return $out;
	}
}
?>
