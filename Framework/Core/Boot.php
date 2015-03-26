<?php
/**
 * Boot class.
 */
class Boot
{
	/**
	 * _appName
	 *
	 * @var mixed
	 * @access private
	 */
	private $_appName;
	/**
	 * _folder
	 *
	 * @var mixed
	 * @access private
	 */
	private $_folder;
	/**
	 * _url
	 *
	 * @var mixed
	 * @access public
	 */
	public $_url;
	/**
	 * _db
	 *
	 * @var mixed
	 * @access private
	 */
	private $_db;
	/**
	 * _controller
	 *
	 * @var mixed
	 * @access public
	 */
	public $_controller;
	/**
	 * _controllerPath
	 *
	 * @var mixed
	 * @access public
	 */
	public $_controllerPath;
	/**
	 * _seoExtention
	 *
	 * (default value: false)
	 *
	 * @var bool
	 * @access public
	 */
	public $_seoExtention=false;
	/**
	 * _pageNotFound
	 *
	 * @var mixed
	 * @access public
	 */
	public $_pageNotFound;
	/**
	 * _route
	 *
	 * @var mixed
	 * @access public
	 */
	public $_route;
	/**
	 * _root
	 *
	 * @var mixed
	 * @access public
	 */
	public $_root;
	/**
	 * _redirect
	 *
	 * @var mixed
	 * @access public
	 */
	public $_redirect;
	/**
	 * post
	 *
	 * @var mixed
	 * @access public
	 */
	public $post;
	/**
	 * get
	 *
	 * @var mixed
	 * @access public
	 */
	public $get;
	/**
	 * header
	 *
	 * @var mixed
	 * @access public
	 */
	public $header;
	/**
	 * server
	 *
	 * @var mixed
	 * @access public
	 */
	public $server;
	/**
	 * cookie
	 *
	 * @var mixed
	 * @access public
	 */
	public $cookie;
	/**
	 * session
	 *
	 * @var mixed
	 * @access public
	 */
	public $session;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $config (default: null)
	 * @return void
	 */
	public function __construct($config=null){
		if($config != null) $this->config($config);
		$this->_controllerPath = APPFOLDER."/controller/";
		$this->_pageNotFound = APPFOLDER."/public/404.html";
		@header('X-Powered-By: BlueJacket');
		@ini_set("expose_php","off");
	}


	/**
	 * init function.
	 *
	 * @access public
	 * @return void
	 */
	public function init(){
		/*loader*/

		$this->loader("Framework/Core/");
		$this->loader(APPFOLDER."/model/");
		$this->loader(APPFOLDER."/controller/");

		$secure = new Security;
		$secure->init(array('get'));

		if(ROUTE_MANAGEMENT){
			$route = new Route;
			if($route->root){
				$this->root($route->root[0],$route->root[1]);
			}

			if(@$route->bind){
				foreach($route->bind as $alias => $newBind){
					$this->bind($alias,array(
							'controller' => $newBind['controller'],
							'default' => $newBind['default'],
							'custom' => $newBind['custom']
						));
				}
			}

			if(@$route->redirect){
				foreach($route->redirect as $alias => $newRedirect){
					$this->redirect($newRedirect[0],$newRedirect[1]);
				}
			}
		}else{
			$this->root(DEFAULT_CONTROLLER,"index");

			$controller_list = scandir(CONTROLLER_DIR);

			foreach($controller_list as $cont){
				if($cont != '..' && $cont != '.' && $cont != 'index.php' && $cont != '.DS_Store'){
					$contr = explode('.',$cont);
					$contr = $contr[0];
					$this->bind($contr,array(
							'controller' => $contr,
							'default' => 'index',
							'custom' => false
						));
				}
			}
		}



		$this->_loadUrl();
	}

	/**
	 * err function.
	 *
	 * @access public
	 * @param mixed $msg
	 * @return void
	 */
	public function err($msg){
		print("<b style='color:red;'>".$msg."</b>");
	}

	/**
	 * config function.
	 *
	 * @access public
	 * @param mixed $ff
	 * @return void
	 */
	public function config($ff){
		if(isset($ff)){
			if(is_file($ff)){
				include $ff;
			}else{
				$this->loader($ff);
			}
		}
	}

	/**
	 * __checkClassFunction function.
	 *
	 * @access public
	 * @param mixed $class
	 * @param mixed $function
	 * @return void
	 */
	public function __checkClassFunction($class,$function){
		$c = get_class_methods($class);
		foreach ($c as $val) {
			if($val == $function){
				return true;
			}
		}
		return false;
	}

	/**
	 * pageNotFound function.
	 *
	 * @access public
	 * @return void
	 */
	public function pageNotFound(){
		if(is_file($this->_pageNotFound)){
			include($this->_pageNotFound);
		}else{
			$this->err("404 file not found!");
		}
	}


	/**
	 * _loadUrl function.
	 *
	 * @access public
	 * @return void
	 */
	public function _loadUrl(){
		//if(!isset($_SERVER['REQUEST_URI'],$_SERVER['SCRIPT_NAME']) return '';

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
				$this->_url[]=htmlspecialchars(stripcslashes(stripslashes($u)));
			}
		}

		$this->readServerParams();

		if(!isset($this->_url[0]) && isset($this->_root['controller'])){
			$controller = $this->_root['controller'];
			$action = $this->_root['action'];
			//require_once $this->_controllerPath.$controller.'.php';
			$this->_controller = new $controller();
			if($this->__checkClassFunction($this->_controller,$action)){
				$this->_controller->$action();
				return;
			}else{
				$this->pageNotFound();
				return;
			}
		}




		if(is_array($this->_redirect)){
			foreach (@$this->_redirect as $alias => $url) {
				if(isset($this->_url[0]) && $this->_url[0] == $alias){
					header("Location: ".$url);
				}
			}
		}

		if(is_array($this->_route)){
			foreach (@$this->_route as $alias => $options) {
				if(isset($this->_url[0]) && $this->_url[0] == $alias){
					$controller=$options['controller'];

					if(is_file($this->_controllerPath.$controller.'.php')){
						//require_once $this->_controllerPath.$controller.'.php';
						$this->_controller = new $controller();

						if($options['custom']){
							if($this->__checkClassFunction($this->_controller,$options['default'])){
								$this->_controller->$options['default']();
								return;
							}else{
								$this->pageNotFound();
								return;
							}
						}else{
							if(isset($this->_url[1])){
								$action = $this->_url[1];
								if($this->__checkClassFunction($this->_controller,$action)){
									if(preg_match("/^[_]/i", $action)){
										$this->pageNotFound();
										return;
									}
									$this->_controller->$action();
									return;
								}
							}else{
								if($this->__checkClassFunction($this->_controller,$options['default'])){
									$this->_controller->$options['default']();
									return;
								}else{
									$this->pageNotFound();
									return;
								}
							}
						}
					}else{
						$this->pageNotFound();
						return;
					}
				}
			}
		}
	}

	/**
	 * _getController function.
	 *
	 * @access public
	 * @param mixed $controller
	 * @param mixed $action
	 * @return void
	 */
	public function _getController($controller,$action){
		if(is_file($this->_controllerPath.$controller.'.php')){
			//require_once $this->_controllerPath.$controller.'.php';
			$this->_controller = new $controller();
			if($this->__checkClassFunction($this->_controller,$action)){
				$this->_controller->$action();
				return;
			}
		}
		return false;
	}

	/**
	 * readServerParams function.
	 *
	 * @access public
	 * @return void
	 */
	public function readServerParams(){
		foreach($_POST as $k => $v){
			$this->post[$k] = $v;
		}
		foreach($_GET as $k => $v){
			$this->get[$k] = $v;
		}
		foreach($_SERVER as $k => $v){
			$this->server[strtolower($k)] = $v;
		}
		if(!@isset($_SESSION['PHPSESSID'])){
			@session_start();
		}
		foreach($_SESSION as $k => $v){
			$this->session[$k] = $v;
		}
		foreach($_COOKIE as $k => $v){
			$this->cookie[$k] = $v;
		}
		foreach(apache_request_headers() as $k => $v){
			$this->header[strtolower($k)] = $v;
		}
	}

	/**
	 * setCookie function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $value
	 * @param mixed $time
	 * @param mixed $folder
	 * @param mixed $domain
	 * @return void
	 */
	public function setcookie($name,$value,$time,$folder,$domain){
		setcookie($name, $value, time()+$time, $folder, $domain, 1);
	}

	/**
	 * deleteCookie function.
	 *
	 * @access public
	 * @param mixed $name
	 * @return void
	 */
	public function deleteCookie($name){
		setcookie($name, "", time()-39000);
	}

	/**
	 * setSession function.
	 *
	 * @access public
	 * @param mixed $key
	 * @param mixed $val
	 * @return void
	 */
	public function setSession($key,$val){
		$_SESSION[$key] = $val;
	}

	/**
	 * deleteSession function.
	 *
	 * @access public
	 * @param mixed $key
	 * @param mixed $val
	 * @return void
	 */
	public function deleteSession($key,$val){
		unset($_SESSION[$key]);
	}

	/**
	 * bind function.
	 *
	 * @access public
	 * @param mixed $alias
	 * @param mixed $controllerArgs
	 * @return void
	 */
	public function bind($alias,$controllerArgs){
		$controller = $controllerArgs['controller'];
		$default = $controllerArgs['default'];
		@$functions = $controllerArgs['functions'];
		@$customController = $controllerArgs['custom'];

		$this->_route[$alias] = array(
			'controller' => $controller,
			'default' => $default,
			'functions' => $functions,
			'custom' => $customController
		);
	}

	/**
	 * root function.
	 *
	 * @access public
	 * @param mixed $controller
	 * @param mixed $action
	 * @return void
	 */
	public function root($controller,$action){
		$this->_root=array(
			'controller' => $controller,
			'action' => $action
		);
	}

	/**
	 * redirect function.
	 *
	 * @access public
	 * @param mixed $alias
	 * @param mixed $url
	 * @return void
	 */
	public function redirect($alias,$url){
		$this->_redirect[$alias] = $url;
	}

	/**
	 * serverUrl function.
	 *
	 * @access public
	 * @return void
	 */
	public function serverUrl(){
		$protocol = isset($_SERVER['HTTPS']) && (strcasecmp('off', $_SERVER['HTTPS']) !== 0);
		if($protocol) $protocol = "https";
		else $protocol = "http";
		$hostname = $_SERVER['SERVER_NAME'];
		$port = $_SERVER['SERVER_PORT'];
		if($port == "80") $port = "";
		else $port = ":".$port;
		return $protocol."://".$hostname.$port;
	}

	/**
	 * loader function.
	 *
	 * @access public
	 * @static
	 * @param mixed $folder
	 * @return void
	 */
	public static function loader($folder){
		if(isset($folder)){
			$fl = scandir($folder);
			foreach($fl as $f){
				if($f != '..' && $f != '.' && $f != 'index.php' && $f != "Boot.php" && $cont != 'index.php'){
					if(is_file($folder.$f)){
						include $folder.$f;
					}
				}
			}
		}
	}
}
?>
