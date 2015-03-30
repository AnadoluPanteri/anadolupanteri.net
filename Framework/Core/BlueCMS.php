<?php 
class BlueCMS 
{ 
	public static function ascii(){
		$text = "<!--
 o.oOOOo.   o      O       o o.OOoOoo        .oOOOo.  Oo      oO .oOOOo.  
 o     o  O       o       O  O             .O     o  O O    o o o     o  
 O     O  o       O       o  o             o         o  o  O  O O.       
 oOooOO.  o       o       o  ooOO          o         O   Oo   O  `OOoo.  
 o     `O O       o       O  O             o         O        o       `O 
 O      o O       O       O  o             O         o        O        o 
 o     .O o     . `o     Oo  O             `o     .o o        O O.    .O 
 `OooOO'  OOoOooO  `OoooO'O ooOooOoO        `OoooO'  O        o  `oooO' 
-->";
		return $text;
	}
	
	
	public $head;
	public $body;
	public $topmenu;
	public $leftmenu;
	
	public function __construct(){
		$this->body=$this->generateBody();
		$this->topmenu = $this->generateTopMenu();	
		$this->topmenu .= $this->generatePluginMenu();	
		$this->leftmenu = $this->generateLeftMenu();	
	}
	
	public function siteInfo(){
		$site = new Configuration();
		$conf = $site->all();
		
		$i=0;
		while($i<count($conf)){
			$new[$conf[$i]['option']] = $conf[$i]['value'];
			$i++;
		}
		return $new;
	}
	
	public function listModules(){
		$modDir = MODULES_FOLDER;
		
		$dirs = scandir($modDir);
		
		foreach($dirs as $dir){
			if($dir != ".." && $dir != "."){
				if(is_file(MODULES_FOLDER."/".$dir."/module.json")){
					$d[$dir] = MODULES_FOLDER."/".$dir."/module.json";
				}
			}
		}
		return $d;
	}
	
	public function listPlugins(){
		$modDir = PLUGINS_FOLDER;
		
		$dirs = scandir($modDir);
		
		foreach($dirs as $dir){
			if($dir != ".." && $dir != "."){
				if(is_file(PLUGINS_FOLDER."/".$dir."/plugin.json")){
					$d[$dir] = PLUGINS_FOLDER."/".$dir."/plugin.json";
				}
			}
		}
		return $d;
	}
	
	public function getModule($module){
		$f = MODULES_FOLDER."/".$module."/module.json";
		if(is_file($f)){
			$file = file_get_contents($f);
			$e = json_decode($file);
			require_once(MODULES_FOLDER."/".$e->$module->name."/".$e->$module->file);
			$md['name'] = $e->$module->name;
			$md['title'] = $e->$module->title;
			$md['file'] = $e->$module->file;
			return $md;
		}
	}
	
	public function getPlugin($plugin){
		$f = PLUGINS_FOLDER."/".$plugin."/plugin.json";
		if(is_file($f)){
			$file = file_get_contents($f);
			$e = json_decode($file);
			require_once(PLUGINS_FOLDER."/".$e->$plugin->name."/".$e->$plugin->file);
			$pl['name'] = $e->$plugin->name;
			$pl['title'] = $e->$plugin->title;
			$pl['file'] = $e->$plugin->file;
			return $pl;
		}
	}
	
	public function getUri(){
		$uri = parse_url($_SERVER['REQUEST_URI']);
	    $query = isset($uri['query']) ? $uri['query'] : '';
			$uri = isset($uri['path']) ? rawurldecode($uri['path']) : '';
	
	
	    if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
			{
				$uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
			}
			elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
			{
				$uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
			}
	
	
	
			$_url = explode('/',$uri);
	
	    if(isset($_url[0]) && $_url[0] == "index" || $_url[0] == "index.php" || $_url[0] == ""){
	      unset($_url[0]);
	    }
	
	
	    if(is_array($_url)){
	      foreach($_url as $u){
	        $url[]=htmlspecialchars(stripcslashes(stripslashes($u)));
	      }
	    }
	    
	    return $url;
	}

	/*
	public function getMenu(){
		$lm = $this->listModules();
		
		$uri = $this->getUri();
		
		
		$html.=null;
		foreach($lm as $nm => $fl){
			$m = $this->getModule($nm);
			
			if($nm == $uri[2]){
				$active = "class='uk-active'";
			}else{
				$active = "";
			}
			
				$html .= '<li '.$active.'>
                        <a href="/index.php/admin/module/'.$nm.'">'.$m['title'].'</a>
                    </li>';
		
		}
		
		$html.='<li class="uk-parent" data-uk-dropdown><a href="">Eklentiler <i class="uk-icon-caret-down"></i></a><div class="uk-dropdown uk-dropdown-navbar"><ul class="uk-nav uk-nav-navbar">';
		
		$lp = $this->listPlugins();
		
		foreach($lp as $nm => $fl){
			$m = $this->getPlugin($nm);
			
			if($nm == $uri[2]){
				$active = "class='uk-active'";
			}else{
				$active = "";
			}
			
				$html .= '<li '.$active.'><a href="/index.php/admin/plugin/'.$nm.'">'.$m['title'].'</a></li>';
		
		}

		$html .= '</ul></div></li>';		
	
		
		
		return $html;
		
		
	}
	*/
	
	public function generateBody(){
		$uri = $this->getUri();
		
		if(isset($uri[2]) && isset($uri[1])){
			switch($uri[1]){
				case 'plugin':
					$plug = $this->getPlugin($uri[2]);
					$pl = new $plug['name']();
					$body = $pl->body();
					break;
				case 'module':
					$module = $this->getModule($uri[2]);
					$md = new $module['name']();
					$body = $md->body();
					break;
			}
			
			return $body;
		}
		
	}
	
	
	public function generateTopMenu(){
		$lm = $this->listModules();
		
		$html=null;
		foreach($lm as $nm => $fl){
			$mod = $this->getModule($nm);
			
			$mc = new $mod['name']();
			
			$html .= $mc->topmenu();
		}
		return $html;
	}
	
	
	public function generateLeftMenu(){
		$uri = $this->getUri();
		
		if(isset($uri[2]) && isset($uri[1])){
			switch($uri[1]){
				case 'plugin':
					$plug = $this->getPlugin($uri[2]);
					$pl = new $plug['name']();
					$menu = $pl->leftmenu();
					break;
				case 'module':
					$module = $this->getModule($uri[2]);
					$md = new $module['name']();
					$menu = $md->leftmenu();
					break;
			}
			
			return $menu;
		}
	}
	
	public function generatePluginMenu(){
		$lm = $this->listPlugins();
		
		$html=null;
		$html.='<li class="uk-parent uk-hidden-small" data-uk-dropdown><a href="">Eklentiler <i class="uk-icon-caret-down"></i></a><div class="uk-dropdown uk-dropdown-navbar"><ul class="uk-nav uk-nav-navbar">';
		foreach($lm as $nm => $fl){
			$mod = $this->getPlugin($nm);
			
			$mc = new $mod['name']();
			
			$html .= $mc->topmenu();
		}
		$html .= '</ul></div></li>';
		return $html;
	}
}
?>