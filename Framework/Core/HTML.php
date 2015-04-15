<?php
/**
 * HTML class.
 */
namespace Bluejacket; 
class HTML
{

	/**
	 * title function.
	 *
	 * @access public
	 * @param mixed $title
	 * @return void
	 */
	public function title($title){
		return '<title>'.$title.'</title>'."";

	}

	/**
	 * keywords function.
	 *
	 * @access public
	 * @param mixed $keywords
	 * @return void
	 */
	public function keywords($keywords){
		return "<meta name=\"keywords\" content=\"".$keywords."\" />";
	}

	/**
	 * author function.
	 *
	 * @access public
	 * @param mixed $author
	 * @return void
	 */
	public function author($author){
		return "<meta name=\"author\" content=\"".$author."\" />";
	}

	/**
	 * description function.
	 *
	 * @access public
	 * @param mixed $desc
	 * @return void
	 */
	public function description($desc){
		return "<meta name=\"description\" content=\"".$desc."\" />";
	}

	/**
	 * html_start function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function html_start(){
		return '<!DOCTYPE html>'."\n".'<html>'."";
	}

	/**
	 * html_end function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function html_end(){
		return '</html>';
	}

	/**
	 * body_start function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function body_start(){
		return '<body>';
	}

	/**
	 * body_end function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function body_end(){
		return '</body>';
	}
	
	
	
	/**
	 * js function.
	 * 
	 * @access public
	 * @param mixed $controller
	 * @param mixed $name
	 * @return void
	 */
	public function js($controller,$name){
		if(isset($controller) && isset($name)){
			return '<script type="text/javascript" src="'.TEMPLATE_FOLDER.$controller.'/'.$name.'.js"></script>';
		}
	}
	
	
	/**
	 * css function.
	 * 
	 * @access public
	 * @param mixed $controller
	 * @param mixed $name
	 * @return void
	 */
	public function css($controller,$name){
		if(isset($controller) && isset($name)){
			return'<link rel="stylesheet" type="text/css" href="/'.TEMPLATE_FOLDER.$controller.'/'.$name.'.css"/>';
		}
	}


	/**
	 * css_template function.
	 *
	 * @access public
	 * @static
	 * @param mixed $obje
	 * @param mixed $templateFolder (default: null)
	 * @return void
	 */
	public static function css_template($obje,$templateFolder=null){
		if(!is_null($templateFolder)) $templateFolder = TEMPLATE_FOLDER."/".$templateFolder."/";
		else $templateFolder = DEFAULT_TEMPLATE_FOLDER;
		if(isset($obje) && $obje!=null){
			return '<link rel="stylesheet" type="text/css" href="/'.$templateFolder.$obje.'.css"/>';
		}
	}


	/**
	 * charset function.
	 *
	 * @access public
	 * @static
	 * @param mixed $type
	 * @return void
	 */
	public static function charset($type){
		if(isset($type) && $type!=null){
			return '<meta http-equiv="content-type" content="text/html;charset='.$type.'" />';
		}
	}


	/**
	 * p_start function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function p_start(){
		return "<p>\n";
	}

	/**
	 * p_end function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function p_end(){
		return "</p>\n";
	}

	/**
	 * b function.
	 *
	 * @access public
	 * @static
	 * @param mixed $text
	 * @return void
	 */
	public static function b($text){
		return "<strong>".$text."</strong>\n";
	}


	/**
	 * h function.
	 *
	 * @access public
	 * @static
	 * @param mixed $text
	 * @param mixed $w (default: null)
	 * @return void
	 */
	public static function h($text,$w=null){
		if(isset($w)){
			return "<h".$w.">".$text."</h".$w.">\n";
		}else{
			return "<h1>".$text."</h1>\n";
		}
	}


	/**
	 * write_log function.
	 *
	 * @access public
	 * @param mixed $what
	 * @param mixed $file (default: null)
	 * @return void
	 */
	public function write_log($what,$file=null){
		if(isset($file)){
			error_log($what,3,$file.".log");
		}else{
			error_log($what,3,$_SERVER['SCRIPT_FILENAME'].".log");
		}
	}


	/**
	 * getTextToHTML function.
	 *
	 * @access public
	 * @param mixed $file
	 * @return void
	 */
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

	/**
	 * favicon function.
	 *
	 * @access public
	 * @param mixed $filename
	 * @return void
	 */
	public function favicon($filename){
		return "<link rel='shortcut icon' href='".$filename.".ico' />\n";
	}

	/**
	 * redirect function.
	 *
	 * @access public
	 * @param mixed $url
	 * @param mixed $time
	 * @return void
	 */
	public function redirect($url,$time){
		return '<meta http-equiv="refresh" content="'.$time.';URL='.$url.'" />'."\n";
	}

	/**
	 * jsdirect function.
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function jsdirect($url){
		return '<script>window.location=\''.$url.'\'</script>';
	}

	/**
	 * direct function.
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function direct($url){
		header("Location: ".$url);
	}

	/**
	 * back function.
	 *
	 * @access public
	 * @param mixed $content
	 * @param mixed $class (default: null)
	 * @return void
	 */
	public function back($content,$class=null){
		echo "<a href='javascript:history.go(-1);' ".(!is_null($class) ? "class='".$class."'" : null).">".$content."</a>";
	}

	/**
	 * alert function.
	 *
	 * @access public
	 * @param mixed $msg
	 * @return void
	 */
	public function alert($msg){
		return "alert('".$msg."');";
	}

	/**
	 * load function.
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function load($url){
		return "<script src=\"".$url."\"></script>";
	}


	/**
	 * json function.
	 *
	 * @access public
	 * @static
	 * @param mixed $object
	 * @param bool $encode (default: true)
	 * @return void
	 */
	public static function json($object,$encode=true){
		if($encode){
			return json_encode($object);
		}else{
			return json_decode($object);
		}
	}

	/**
	 * generateTags function.
	 *
	 * @access public
	 * @param mixed $model
	 * @return void
	 */
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
