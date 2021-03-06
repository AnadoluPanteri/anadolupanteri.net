<?php
/**
 * Controller class.
 */
namespace Bluejacket; 
class Controller
{
	/**
	 * html
	 *
	 * @var mixed
	 * @access public
	 */
	public $html;
	/**
	 * view
	 *
	 * @var mixed
	 * @access public
	 */
	public $view;
	/**
	 * form
	 *
	 * @var mixed
	 * @access public
	 */
	public $form;
	/**
	 * url
	 *
	 * @var mixed
	 * @access public
	 */
	public $url;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){
		$this->view = new View(strtolower(self::getClassName()));
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

		$this->_url = explode('/',$uri);

		if(isset($this->_url[0])
			&& $this->_url[0] == "index"
			|| $this->_url[0] == "index.php"
			|| $this->_url[0] == ""){
			unset($this->_url[0]);
		}

		if(SSL_ACTIVE){
			if($_SERVER['HTTPS']!="on"){
				$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				header("Location:$redirect");
			}
		}

	}
	
	/**
	 * getClassName function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function getClassName() {
        return get_called_class();
    }
    
    
    /**
	 * getPageLimits function.
	 *
	 * @access public
	 * @param mixed $count
	 * @return void
	 */
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
}
?>
