<?php
class Bower
{
	public $componentsFolder='Application/bower_components';
	public $target="dist";
	public $folder=null;
	public $html=null;
	function __construct($name,$target,$componentsFolder){
		if(isset($compentsFolder)){
			$this->folder .= $compentsFolder;
		}else{
			$this->folder .= $this->componentsFolder;
		}
		
		if(isset($name)){
			$this->folder .= "/".$name;
		}
		
		print_r($folder);
	
		if(is_file($this->folder."/bower.json")){
			if(isset($target)){
				$this->folder .= "/".$target;
			}else{
				$this->folder .= "/".$this->target;
			}
		}
	}
	
	
	public function generate($files = array(),$type = array()){
		if(is_array($type)){
			foreach($type as $t){
				if($t=="css"){
					if(is_array($files)){
						foreach($files as $file){
							$this->html .= $this->css($this->folder."/".$file.".css");
						}
					}else{
						$this->html .= $this->css($this->folder."/".$files.".css");
					}
					
				}
				
				if($t=="js"){
					if(is_array($files)){
						foreach($files as $file){
							$this->html .= $this->js($this->folder."/".$file.".js");
						}
					}else{
						$this->html .= $this->js($this->folder."/".$files.".js");
					}
					
				}
			}
		}
		return $this->html;
	}


  public function css($obje){
    if(isset($obje) && $obje!=null){
      return '<link rel="stylesheet" type="text/css" href="/'.$obje.'"/>';
    }
  }
  
  public function js($obje){
		if(isset($obje) && $obje!=null){
			return '<script type="text/javascript" src="/'.$obje.'"></script>';
		}
	}		
}
?>