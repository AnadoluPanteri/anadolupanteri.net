<?php
/**
 * API class.
 */
namespace Bluejacket;  
class API
{

	/**
	 * users
	 *
	 * @var mixed
	 * @access public
	 */
	public $users;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($params=null){
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

		$this->model = $this->_url[2];
		$this->id = $this->_url[3];
		$this->options = explode("|",$_GET['options']);
		unset($_GET['options']);
		foreach ($_GET as $key => $val){
			$this->where[$key]=$val;
		}
		
		if($params==null){
			$this->params = $_POST;
		}else{
			$this->params = $this->objectToArray($params);
		}
	}
	
	
	/**
	 * objectToArray function.
	 * 
	 * @access public
	 * @param mixed $object
	 * @return void
	 */
	function objectToArray($object){
		if(is_object($object)){
			$object = get_object_vars($object);
		}
		if(is_array($object)){
			return array_map(array($this,__FUNCTION__),$object);
		}else{
			return $object;
		}
	}

	/**
	 * basicAuth function.
	 *
	 * @access public
	 * @return void
	 */
	public function basicAuth(){
		$username = null;
		$password = null;

		// mod_php
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];



			// most other servers
		} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {

			if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'basic')===0)
				list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

		}

		if (is_null($username)) {
			header('WWW-Authenticate: Basic realm="API login"');
			header('HTTP/1.0 401 Unauthorized');
			die();
		}else{
			/* domain doğrulaması */
			if(isset($this->users[$username])){
				if($this->users[$username]!=$password){
					header('HTTP/1.0 401 Unauthorized');
					die();
				}
			}else{
				header('HTTP/1.0 401 Unauthorized');
				die();
			}
		}

	}

	/**
	 * addUser function.
	 *
	 * @access public
	 * @param mixed $config
	 * @return void
	 */
	public function addUser($config){
		foreach ($config as $username => $password){
			$this->users[$username] = $password;
		}
	}



	/**
	 * method function.
	 *
	 * @access public
	 * @return void
	 */
	public function method(){
		$method = $_SERVER['REQUEST_METHOD'];
		switch($method) {
		case 'PUT':
			return $this->put();
			break;
		case 'POST':
			return $this->post();
			break;
		case 'DELETE':
			return $this->delete();
			break;

		case 'GET':
			return $this->get();
			break;

		default:
			header('HTTP/1.1 405 Method Not Allowed');
			header('Allow: GET, PUT, DELETE, POST');
			break;
		}
	}

	/**
	 * delete function.
	 *
	 * @access public
	 * @return void
	 */
	public function delete(){
		$model = $this->model;
		$delete = new $model();
		if($delete->delete($this->id)) $result['status'] = true;
		else $result['status'] = false;
		return json_encode($result);
	}
	/**
	 * put function.
	 *
	 * @access public
	 * @return void
	 */
	public function put(){
		$model = $this->model;
		parse_str(file_get_contents("php://input"),$post_vars);
		$put = new $model($post_vars);

		$result['status'] = false;
		if(isset($this->id)){
			if($put->update($this->id)){
				$result['status'] = true;
			}
		}
		return json_encode($result);
	}

	/**
	 * get function.
	 *
	 * @access public
	 * @return void
	 */
	public function get(){
		$model = $this->model;
		$get = new $model();
		if(is_numeric($this->id)){
			$get->find($this->id);
			if($get->_def){
				$result['status'] = true;
				foreach ($get->_def as $key => $val){
					if(!is_numeric($key)){
						$data[$key] = $val;
					}
				}
				$result['data'] = $data;

			}else{
				$result['status'] = false;
			}
		}else{
			$limit = null;
			$where = null;
			$group = null;
			$order = null;

			if(is_array($this->options)){
				foreach ($this->options as $option) {
					$opt = explode(":",$option);
					$val = explode(",",$opt[1]);
					if($opt[0] == "order"){
						$order = array($val[0],$val[1]);
					}

					if($opt[0] == "limit"){
						$limit = array($val[0],$val[1]);
					}

					if($opt[0] == "group"){
						$group = array($val[0],$val[1]);
					}
				}
			}

			if(isset($this->where)){
				$where = $this->where;
			}

			
			$out = $get->special(array(
				"where" => $where,
				"order" => $order,
				"limit" => $limit,
				"groupBy" => $group
			));

			if(is_array($out)){
				$result['status'] = true;
				$i=0;
				while($i<count($out)){
					foreach ($out[$i] as $key => $val){
						if (!is_numeric($key)) {
							$result['data'][$i][$key] = $val;
						}
					}
					$i++;
				}
			}else{
				$result['status'] = false;
			}
		}

		return json_encode($result);
	}

	/**
	 * post function.
	 *
	 * @access public
	 * @return void
	 */
	public function post(){
		$model = $this->model;
		$post = new $model($this->params);

		$result['status'] = false;
		if(isset($this->id)){
			if($post->update($this->id)){
				$result['status'] = true;
			}
		}else{
			if($post->save()){
				$result['status'] = true;
			}
		}
		return json_encode($result);
	}
}
?>
