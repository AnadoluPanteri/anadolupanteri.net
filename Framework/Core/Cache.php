<?php
class Cache
{
  public $_folder = CACHE_FOLDER;
  public $_time = CACHE_TIMER;
  public $_filename;
  public $_ctime;
  public $_cname;
  private $fb;

  function __construct(){
		$this->_filename = md5($_SERVER['REQUEST_URI']).".html";
		$this->_cname = $this->_folder."/".$this->_filename;
		$this->_ctime = $this->_time * 60 * 60;

    if (file_exists($this->_cname)){
      if(time() - $this->_ctime < filemtime($this->_cname)){
        readfile($this->_cname);
        exit();
      }else{
           unlink($this->_cname);
      }
		}
		ob_start();
  }

  function end(){
    $this->fp = fopen($this->_cname, 'w+');
  	fwrite($this->fp, ob_get_contents());
  	fclose($this->fp);
  	ob_end_flush();
  }
}
?>
