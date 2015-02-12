<?php
class View
{
	private $_time=0;
	private $_templateFile;
	private $_vars;
	private $_debug;
	private $_loadTime;
	private $_partials;
	private $_functions;
	private $_call;
	public $themeFolder = TEMPLATE_FOLDER;

	public function set($s,$v){
		$this->_vars[$s]=$v;
	}

	public function change($s,$v){
		if(isset($this->_vars[$s])){
			$this->_vars[$s]=$v;
		}
	}

	/* set partials */
	public function partial($v,$f){
		$this->_partials[$v] = $f;
	}

	/* set functions */
	public function call($v,$f,$c){
		$this->_call[$c][$v] = $f;
	}

	/* generate cache name */
	public function _generateCacheName($template,$folder=null){
		if(!is_null($folder)){
			$f1=$this->themeFolder."/".$folder."/";
		}else{
			$f1=DEFAULT_TEMPLATE_FOLDER;
		}
		@$this->_templateFile=$f1.$template.'.html';
	}

	/* get template */
	/* MAKE: Cache Subclass */
	public function _template($file){
			ob_start();
				extract($this->_vars);
	 			include($file);
	    		$output = ob_get_contents();
	    	ob_end_clean();
	    	echo $output;
	}


	/* DEVRIM! */
	public function _applyVars($file){
		$source = file_get_contents($file);
		$html = $this->_applyPartials($source);
		$html = $this->_changeContents($html);
		return $html;
	}

	public function _applyPartials($html){
		foreach ($this->_partials as $key => $val) {
			$source = file_get_contents($val);
			$html = $this->_changeContents($html);
			$html = str_replace('{=' . $key . '}', $source, $html);
		}
		return $html;
	}

	/* içerik değiştirme sınıfı */
	public function _changeContents($source){
		foreach ($this->_vars as $key2 => $value2) {
			if(@!is_array($value) && @!is_array($key)){
				@$source = str_replace('{$ ' . $key2 . ' }', $value2, $source);
			}
		}
		@$source = str_replace($this->_dropTag('{$ ',' }',$source), '', $source);

		if($this->_call){
			foreach ($this->_call as $key3 => $val3) {
				if(class_exists($key3)){
					$cont = new $key3;
				}else{
					include 'controller/'.$key3.'.php';
					$cont = new $key3;
				}
				foreach($val3 as $key4 => $value4){
					$source = str_replace('{func ' . $key4 . ' }', $cont->$key4(), $source);
				}
			}
		}



		$source = str_replace($this->_dropTag("{* "," *}",$source), "<!-- ".$this->_getTagContent("{* "," *}",$source)." -->", $source);
		$source = str_replace($this->_dropTag("{php}","{/php}",$source), eval($this->_getTagContent("{php}","{/php}",$source)), $source);

		return $source;
	}

	public function _each($arr,$key,$val,$out){
		$all = null;
		foreach($arr as $key2 => $val2){
			$out = str_replace($key,$key2,$out);
			$out = str_replace($val,$val2,$out);
			$all .= $out;
		}
		return $all;
	}


	public function _getTagContent($start,$end,$content){
		@$tag = explode($start,$content);
		@$tag = explode($end,$tag[1]);
		return $tag[0];
	}

	public function _dropTag($start,$end,$content){
		return $start.''.$this->_getTagContent($start,$end,$content).''.$end;
	}


	/*
		yeni load :: yeni versiyonlarda bu şekilde olacak.
	*/
	public function load($template,$folder=null){
		$this->_generateCacheName($template,$folder);
		ob_start();
			echo $this->_applyVars($this->_templateFile);
	    	$output = ob_get_contents();
	    ob_end_clean();
	    echo $output;
	}

	public function getPageLimits($count){
		if(isset($_GET['page'])){
			$current = ($_GET['page']-1)*$count;
			$next = $current+$count;
			//var_dump($current);
			//var_dump($next);

			return array($current,$count);
		}else{
			return array(0,$count);
		}
	}

	public function parse($file){
		ob_start();
			$source = file_get_contents($file);
			$html = $this->_applyPartials($source);
			$html = $this->_changeContents($html);
			echo $html;
	    	$output = ob_get_contents();
	    ob_end_clean();
	    return $output;
	}
	
	public function arraySet($s,$a){
		if(is_array($a)){
			foreach($a as $k => $v){
				$this->set($s."[".$k."]",$v);
			}
		}
	}
}
?>
