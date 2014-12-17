<?php
class HTML
{
    public function title($title){
		return '<title>'.$title.'</title>'."";

	}

	public function keywords($keywords){
		return "<meta name=\"keywords\" content=\"".$keywords."\" />";
	}

    public function author($author){
		return "<meta name=\"author\" content=\"".$author."\" />";
	}

    public function description($desc){
		return "<meta name=\"description\" content=\"".$desc."\" />";
	}

    public static function html_start(){
        return '<!DOCTYPE html>'."\n".'<html>'."";
    }

    public static function html_end(){
        return "".'</html>'."";
    }

    public static function body_start(){
        return "".'<body>'."";
    }

    public static function body_end(){
        return "".'</body>'."";
    }

	public function js_file($obje){
		if(isset($obje) && $obje!=null){
			return "\t".'<script type="text/javascript" src="'.$folder.'js/'.$obje.'.js"></script>'."\n";
		}
	}


	public function css_file($obje,$adds=null){
		if(isset($obje) && $obje!=null){
			return "\t".'<link rel="stylesheet" type="text/css" href="'.$folder.'css/'.$obje.'.css" '.$adds.'/>'."\n";
		}
	}



	public static function js_custom($obje){
		if(isset($obje) && $obje!=null){
			return "\t".'<script type="text/javascript" src="'.$obje.'.js"></script>'."\n";
		}
	}


	public static function css_custom($obje){
		if(isset($obje) && $obje!=null){
			return "\t".'<link rel="stylesheet" type="text/css" href="'.$obje.'.css"/>'."\n";
		}
	}


  public static function css($obje){
    if(isset($obje) && $obje!=null){
      return '<link rel="stylesheet" type="text/css" href="'.$obje.'"/>';
    }
  }


	public static function charset($type){
		if(isset($type) && $type!=null){
			return '<meta http-equiv="content-type" content="text/html;charset='.$type.'" />';
		}
	}


    public static function p_start(){
        return "<p>\n";
    }

    public static function p_end(){
        return "</p>\n";
    }

    public static function b($text){
        return "<strong>".$text."</strong>\n";
    }


    public static function h($text,$w=null){
        if(isset($w)){
            return "<h".$w.">".$text."</h".$w.">\n";
        }else{
            return "<h1>".$text."</h1>\n";
        }
    }

	// log uzantılı dosya yazdırır
	public function write_log($what,$file=null){
		if(isset($file)){
			error_log($what,3,$file.".log");
		}else{
			error_log($what,3,$_SERVER['SCRIPT_FILENAME'].".log");
		}
	}


  public function getTextToHTML($file){
	    if(file_exists($file)){
			$fo=fopen($file,"r");
			$fs=filesize($file);
			$fget=fread($fo,$fs);
			fclose($fo);
			return nl2br($fget);
		}
		return false;
	}

	public function favicon($filename){
		return "<link rel='shortcut icon' href='".$filename.".ico' />\n";
	}

    public function redirect($url,$time){
        return '<meta http-equiv="refresh" content="'.$time.';URL='.$url.'" />'."\n";
    }

    public function jsdirect($url){
    	return '<script>window.location=\''.$url.'\'</script>';
    }

    public function hdirect($url){
    	header("Location: ".$url);
    }

    public function back($content,$class=null){
    	echo "<a href='javascript:history.go(-1);' ".(!is_null($class) ? "class='".$class."'" : null).">".$content."</a>";
    }

    public function alert($msg){
      return "alert('".$msg."');";
    }

    public function load($url){
      return "<script src=\"".$url."\"></script>";
    }


  	public static function json($object,$encode=true){
  		if($encode){
  			return json_encode($object);
  		}else{
  			return json_decode($object);
  		}
  	}

    public function generateTags($model){
  		$mod = new Model($model);
  		$data = $mod->__oget(null,array("tag",true),null);
  		$last_key=key(array_slice($data, -1,1, TRUE));

  		$i=0;
  		while($i<count($data)){
  			$output.=$data[$i]['tag'];
  			if($i!=$last_key){
  				$output.=", ";
  			}
  			$i++;
  		}
  		return $output;
  	}

    
}
?>
