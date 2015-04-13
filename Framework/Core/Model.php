<?php
/**
 * Model class.
 */
class Model
{
	/**
	 * __construct function.
	 *
	 * @access public
	 * @param Array $properties (default: array())
	 * @return void
	 */
	public function __construct(array $properties=array()){
		$this->error = new Error();
		$this->db = new DB();
		$this->db->table($this->table());

		foreach($properties as $key => $value){
			$this->{$key} = $value;
			$this->_def[$key]=$value;
		}
	}


	/**
	 * destroy function.
	 *
	 * @access public
	 * @return void
	 */
	public function destroy(){
		$this->db->drop();
	}

	/**
	 * setVars function.
	 *
	 * @access public
	 * @param Array $properties (default: array())
	 * @return void
	 */
	public function setVars(array $properties=array()){
		foreach($properties as $key => $value){
			$this->{$key} = $value;
			$this->_def[$key]=$value;
		}
	}



	/**
	 * delete function.
	 *
	 * @access public
	 * @param int $id (default: 0)
	 * @return void
	 */
	public function delete($id=0){
		$this->db->delete();
		if(is_array($id)){
			$this->db->where($id);
			$this->db->run();
		}else if(is_numeric($id)){
				$this->db->where(array('id' => $id));
				$this->db->run();
			}
	}


	/**
	 * save function.
	 *
	 * @access public
	 * @return void
	 */
	public function save(){
		$this->db->insert($this->_def);
		if($this->db->run()){
			$this->find($this->db->getLastInsertedId());
			return true;
		}
		return false;
	}


	/**
	 * find function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function find($id){
		$this->db->select();
		$this->db->where(array('id' => $id));

		$this->db->query();
		$arr = $this->db->output->fetch();
		if($arr){
			foreach ($arr as $key => $val) {
				@$this->{$key} = $val;
				@$this->_def[$key] = $val;
			}
			return true;
		}
		return false;
	}


	/**
	 * get function.
	 *
	 * @access public
	 * @param mixed $array
	 * @param mixed $order (default: null)
	 * @return void
	 */
	public function get($array,$order=null){
		$pk = $this->getPrimaryKey();
		$this->db->select();
		if(is_array($array)){
			$this->db->where($array);
		}

		if(!is_null($order)){
			if(is_array($order)){
				$column = $order['column'];
				$desc = isset($order['desc']) ? $order['desc'] : false;
				$asc = isset($order['asc']) ? $order['asc'] : false;
				if($desc){
					$opt = false;
				}else if($asc){
						$opt = true;
					}else{
					$opt = false;
				}

				$this->db->orderBy($colmn,$opt);
			}
		}else{
			$this->db->orderBy($pk,false);
		}
		$this->db->query();

		$out = $this->db->output->fetch();
		if(is_array($out)){
			foreach ($out as $key => $val) {
				$this->{$key} = $val;
				$this->_def[$key] = $val;
			}
			return $out;
		}


		return false;
	}


	/**
	 * all function.
	 *
	 * @access public
	 * @param mixed $array (default: null)
	 * @return void
	 */
	public function all($array=null){
		$this->db->select();
		if($array != null && is_array($array)){
			$this->db->where($array);
		}
		$this->db->query();
		$result = @$this->db->output ? $this->db->output->fetchAll() : false;
		return $result;
	}

	/**
	 * all function.
	 *
	 * @access public
	 * @param mixed $array (default: array)
	 * @return void
	 */
	public function special($options=array(
			"select" => array(),
			"where" => array(),
			"whereOpts" => array("exclude" => array(), "or" => null),
			"groupBy" => array(),
			"orderBy" => array(),
			"limit" => array(),
			"extra" => null
		)){

		if(is_array($options)){
			if(isset($options['select']) && is_array($options['select']))
				$this->db->select($options['select']);
			else
				$this->db->select();

			if(isset($options['where']) &&  is_array($options['where']))
				if(isset($options['whereOpts']) && is_array($options['whereOpts'])){
					$or = is_bool($options['whereOpts']['or']) ?  $options['whereOpts']['or'] : false;
					$exclude = is_array($options['whereOpts']['exclude']) ?  $options['whereOpts']['exclude'] : null;
					$this->db->where($options['where'],$exclude,$or);
				}else{
				$this->db->where($options['where']);
			}




			if(isset($options['extra']) && !is_null($options['extra']))
				$this->db->extra($options['extra']);

			if(isset($options['groupBy']))
				$this->db->groupBy($options['groupBy']);

			if(isset($options['orderBy'][0]) && is_bool($options['orderBy'][1]))
				$this->db->orderBy($options['orderBy'][0],$options['orderBy'][1]);

			if(isset($options['limit'][0]) && is_numeric($options['limit'][0]) && is_numeric($options['limit'][1]))
				$this->db->limit($options['limit'][0],$options['limit'][1]);



			$run = true;
		}else if(isset($options)){
				$this->db->custom($options);

				$run = true;
			}else{
			$run = false;
		}



		if($run){
			$this->db->query();
			$result = @$this->db->output ? $this->db->output->fetchAll() : false;
			return $result;
		}
		return false;
	}

	/**
	 * count function.
	 *
	 * @access public
	 * @param mixed $arr (default: null)
	 * @return void
	 */
	public function count($arr=null){
		$this->db->count();
		if(is_array($arr)) $this->db->where($arr);
		$this->db->query();
		$result = @$this->db->output ? $this->db->output->fetch() : false;
		$result = $result ? $result[0] : false;
		return $result;
	}



	/**
	 * update function.
	 *
	 * @access public
	 * @param int $id (default: 0)
	 * @return void
	 */
	public function update($id=0){
		if($this->_def){
			$this->db->update($this->_def);

			if(is_numeric($id)) $this->db->where(array("id"=>$id));
			else if(is_array($id)) $this->db->where($id);

				return $this->db->run();
		}
	}


	/**
	 * getPrimaryKey function.
	 *
	 * @access public
	 * @return void
	 */
	public function getPrimaryKey(){
		$this->db->keys();
		$this->db->where(array('Key_name' => 'PRIMARY'));
		$this->db->query();
		$result = @$this->db->output ? $this->db->output->fetch() : false;
		$result = $result ? $result['Column_name'] : false;
		return $result;
	}

	/**
	 * searchQuery function.
	 *
	 * @access public
	 * @param mixed $query
	 * @param mixed $config
	 * @return void
	 */
	public function searchQuery($query,$config){
		foreach ($this->search() as $key) {
			$sq[$key] = $query;
		}
		$this->db->search($sq,$config);
		$this->db->query();
		return $this->db->output->fetchAll();
	}

	/*
	public function moveData($model2){
		return false;
	}
	*/


	/**
	 * getLastData function.
	 *
	 * @access public
	 * @return void
	 */
	public function getLastData(){
		$this->db->_query="SELECT * FROM ".$this->db->_table." ORDER BY ". $this->getPrimaryKey()." DESC LIMIT 1,1";
		$this->db->query();
		$result = @$this->db->output ? $this->db->output->fetch() : false;
		return $result;
	}
	
	public function sum($arr=array()){
		$this->db->sum($arr);
		$this->db->query();
		$result = @$this->db->output ? $this->db->output->fetch() : false;
		return $result;
	}
}
?>
