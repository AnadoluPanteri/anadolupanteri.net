<?php
class Registers
{
  function __construct(){
    if(!isset($_SESSION)){
      @session_start();
    }
   @$_SESSION['phpsessid'] = $_COOKIE['PHPSESSID'];
   @$_COOKIE['session'] = true;
   foreach($_SESSION as $key => $val){
     $this->{$key} = $val;
   }
  }

  public function register($array,$timer=null){
    if(is_array($array)){
      foreach($array as $key => $val){
        if($key == "password" || $key == "pass" || $key == "key"){
          $_SESSION[$key] = md5($val);
        }else{
          $_SESSION[$key] = $val;
        }
        if(@$timer!=null) setcookie($key,base64_encode($val),time()+60*60*$timer);
      }
    }
  }

  public function get($key){
    if(@isset($_SESSION[$key])){
      return $_SESSION[$key];
    }else if(@isset($_COOKIE[$key])){
      return base64_decode($_COOKIE[$key]);
    }
    return false;
  }

  public function clearRegisters(){
    if(!isset($_SESSION)){
      @session_start();
    }
    foreach($_SESSION as $key => $val){
      unset($this->{$key});
      unset($_SESSION[$key]);
    }
    return true;
  }

  public function drop($key){
    if(@isset($_SESSION[$key])){
      unset($_SESSION[$key]);
      return true;
    }
    return false;
  }
}
?>
