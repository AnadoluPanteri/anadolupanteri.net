<?php
class Translate
{
  public function t($key){
    return $this->{$key};
  }

  public function c($key,$spinf,$change){
    return str_replace($spinf,$change,$this->t($key));
  }
}
?>
