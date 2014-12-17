<?php
class YahooWeather
{
  public $code;
  function code($code){
    $this->code = $code;
  }

  function setCity($city){
    foreach($this->trCodeList as $k => $v){
      if($city == strtolower($v)){
        $this->code($k);
      }
    }
  }

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
