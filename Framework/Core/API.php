<?php
class API
{ 

    public $users;

    public function __construct(){
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
    }
    
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

    public function addUser($config){
        foreach ($config as $username => $password){
            $this->users[$username] = $password;
        }
    }



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

    public function delete(){
        $model = $this->model;
        $delete = new $model();
        if($delete->delete($this->id)) $result['status'] = true;
        else $result['status'] = false;
        return json_encode($result);
    }
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
    
    /*
     * GET DATA API USE 
     * 
     *  <script name>/<custom route>/<model>/<id|options>&<where array>&<where array>...
     *  @mode: your model
     *  @id: numeric
     *  @options: ?options=order:<row>,<true|false>|limit:<start>,<end>|group:<row>,<true|false>
     *  @where array: &<row>=<value>
     * 
     *  example: get users, order by name, show only 10 data
     *  /index.php/json/users/?options=order:name,true|limit:0,10
     * 
     *  example: get 1. user
     *  /index.php/json/users/1
     * 
     *  example: show 'odiac' users
     *  /index.php/json/users/?name=odiac
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
            
            
            
            $out = $get->special($where,$order,$limit,$group);
            
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

    public function post(){
        $model = $this->model;
        $post = new $model($_POST);

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
