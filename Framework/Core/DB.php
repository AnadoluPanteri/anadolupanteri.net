<?php
class DB
{
	private $pdo;
	private $boot;
	public $_query;
	public $output;
	public $_table;
	public $count;
	public $_config = array();

	public function __construct(){
		$this->boot = new Boot();

		$this->_config = array(
			"driver" => DB_DRIVER,
			"server" => DB_SERVER,
			"database" => DB_DATABASE,
			"username" => DB_USERNAME,
			"password" => DB_PASSWORD,
			"port" => DB_PORT
		);

		$this->_connect();
	}

	public function _connect(){
		try {
				$this->pdo = new PDO($this->_config['driver'].':host='.$this->_config['server'].';port='.$this->_config['port'].';dbname='.$this->_config['database'], $this->_config['username'], $this->_config['password']);
		} catch (\PDOException $e) {
			if(APP_DEBUGING){
					$this->boot->err("Connection failed: ".$e->getMessage());
				}
		}
		//$this->pdo->exec("SET NAMES UTF8");
		//$this->pdo->exec("SET CHARACTER SET UTF8");
	}

	public function changeConnection($server,$username,$password,$database,$driver=null){
		$this->_config['server'] = $server;
		$this->_config['username'] = $username;
		$this->_config['password'] = $password;
		$this->_config['database'] = $database;
		if(!is_null($driver)) $this->_config['driver'] = $driver;

		$this->_connect();
	}

	public function changeDb($db){
		$this->_config['database'] = $db;
		$this->_connect();
	}

	public function getLastInsertedId(){
		$out = $this->pdo->lastInsertId();
		return $out;
	}

	public function query(){
		if($out = $this->pdo->query($this->_query)){
			$this->output = $out;
			if(APP_DEBUGING){
				$this->boot->err('Query: '.$this->_query);
			}
		}else{
			if(APP_DEBUGING){
				$error = $this->pdo->errorInfo();
				$this->boot->err('Query Failed: '.$error[2]);
				$this->boot->err('<br> Query: '.$this->_query);
			}
		}
	}

	public function run(){
		if($this->pdo->exec($this->_query)){
			return true;
		}else{
			if(APP_DEBUGING){
				$error = $this->pdo->errorInfo();
				$this->boot->err('Query Failed: '.$error[2]);
				$this->boot->err('<br> Query: '.$this->_query);
			}
			return false;
		}
	}

	public function table($name){
		$this->_table = $name;
	}

	public function select($array=null){
		if(is_array($array)){
			$selector=null;
			$last_key=key(array_slice($array, -1,1, TRUE));
			foreach($array as $key => $val){
				$selector.="$val";
				if($key!=$last_key){
					$selector.=",";
				}
			}
		}else{
			$selector = "*";
		}

		$this->_query = "SELECT  $selector  FROM ".$this->_table;
	}

	public function delete(){
		$this->_query = "DELETE FROM ".$this->_table;
	}

	public function count(){
		$this->_query = "SELECT count(*) as count FROM ".$this->_table;
	}

	public function insert($data){
		$this->_query = "INSERT INTO ".$this->_table;
		$output=null;
		$last_key=key(array_slice($data, -1,1, TRUE));
		if(is_array($data)){
			$output.="  (";
			foreach($data as $key => $value){
				$output.="`$key`";
				if($key!=$last_key){
					$output.=", ";
				}
			}
			$output.=") VALUES (";
			foreach($data as $key => $value){
				$output.="'$value'";
				if($key!=$last_key){
					$output.=", ";
				}
			}
			$output.=");";
			$this->_query .= $output;
		}
	}

	public function where($data,$exclude=null,$or=false){
		$output=null;
		if(is_array($data)){
			$last_key=key(array_slice($data, -1,1, TRUE));
			foreach($data as $key => $value){
				$output.="`$key`='$value'";
				if($key!=$last_key){
					if($or) $output.=" OR ";
					else $output.=" AND ";
				}
			}
		}
		if(is_array($exclude)){
			$last_key2=key(array_slice($exclude, -1,1, TRUE));
			foreach($exclude as $key => $value){
				$output.="`$key`!='$value'";
				if($key!=$last_key2){
					$output.=" AND ";
				}
			}
		}
		$this->_query .= " WHERE ".$output;
	}


	public function create($data){
		$q_create_table="CREATE TABLE IF NOT EXISTS `".$this->_table."`";
		$q_create_table.="(";
		$output=null;
		$last_key=key(array_slice($data, -1,1, TRUE));
		if(is_array($data)){
			foreach($data as $key => $value){
				$output.="`$key` $value";
				if($key!=$last_key){
					$output.=", ";
				}
			}
			$q_create_table .= $output;
		}
		$q_create_table.=")";
		$this->_query .= $q_create_table;
	}

	public function drop(){
		$this->_query = "DROP TABLE ".$this->_table;
	}

	public function colmns(){
    	$this->_query = "SHOW COLUMNS FROM ".$this->_table;
    }

    public function alter($data){
    	$this->_query = "ALTER ".$this->table;
    	foreach ($data as $key => $value) {
    		$this->_query .= $value."(".$key.")";
    	}
    }

    public function orderBy($object=null,$asc=true){
    	if($asc) $asc = "ASC";
    	else $asc = "DESC";
    	$this->_query .= " ORDER BY ".$object." ".$asc;
    }

    public function groupBy($object){
	    if(isset($object)) $this->_query .= " GROUP BY ".$object;
    }

    public function limit($start=0,$end=200){
    	$this->_query .= " LIMIT ".$start.",".$end;
    }

    public function update($data){
    	$this->_query = "UPDATE ".$this->_table." SET ";

    	$output=null;
		$last_key=key(array_slice($data, -1,1, TRUE));
		if(is_array($data)){
			foreach($data as $key => $value){
				$output.="$key='$value'";
				if($key!=$last_key){
					$output.=", ";
				}
			}
		}
		$this->_query .= $output;
    }

    public function extra($extra){
    	$this->_query .= $extra;
    }

    public function keys(){
    	$this->_query = "SHOW KEYS FROM ".$this->_table;
    }

		public function columns(){
			$this->_query = "SHOW COLUMNS FROM ".$this->_table;
		}

		/*
    public function search($data,$filter=null,$extra=null){
    	$this->_query = "SELECT * FROM ".$this->_table." WHERE ";

    	$output=null;
		$last_key=key(array_slice($data, -1,1, TRUE));
		if(is_array($data)){
			foreach($data as $key => $value){
				$output.="$key LIKE '%$value%'";
				if($key!=$last_key){
					$output.=" OR ";
				}
			}
		}

		$last_key=key(array_slice($filter, -1,1, TRUE));
		if(is_array($filter)){
			$output.=" AND ";
			foreach($filter as $key => $value){
				$output.="$key='%$value%'";
				if($key!=$last_key){
					$output.=" AND ";
				}
			}
		}

		if(!is_null($extra)){
			$output.=$extra;
		}

		$this->_query .= $output;
    }
		*/

		public function search($data=null,$config=array(
			"regexp" => false,
			"filter" => null,
			"extra" => null,
			"or" => false
		)){
			$output = null;
			$this->_query = "SELECT * FROM ".$this->_table;
			if($data!=null){
				$this->_query.=" WHERE ";
			}else{
				return;
			}
			
			
			if($config['filter']){
				$last_key=key(array_slice($config['filter'], -1,1, TRUE));
				if(is_array($config['filter'])){
					foreach($config['filter'] as $key => $value){
						$output.="$key='$value'";
						$output.= " AND ";
						/*
						if($key!=$last_key){
							$output.= " AND ";
						}
						*/
					}
				}
			}

			if($config['regexp']){
				$last_key=key(array_slice($data, -1,1, TRUE));
				if(is_array($data)){
					foreach($data as $key => $value){
						$output.="$key REGEXP '$value'";
						if($key!=$last_key){
							$output.= $config['or'] ? " OR " : " AND ";
						}
					}
				}
			}else{
				$last_key=key(array_slice($data, -1,1, TRUE));
				if(is_array($data)){
					foreach($data as $key => $value){
						$output.="$key LIKE '%$value%'";
						if($key!=$last_key){
							$output.=" OR ";
						}
					}
				}
			}

			if(!is_null($config['extra'])){
				$output.=$config['extra'];
			}

			$this->_query .= $output;

		}
		
		public function custom($query){
			$this->_query = $query;
		} 

		/*
    public function regexp($data,$filter=null,$extra=null){
    	$this->_query = "SELECT * FROM ".$this->_table." WHERE ";

    	$output=null;
		$last_key=key(array_slice($data, -1,1, TRUE));
		if(is_array($data)){
			foreach($data as $key => $value){
				$output.="$key REGEXP '$value'";
				if($key!=$last_key){
					$output.=" OR ";
				}
			}
		}

		$last_key=key(array_slice($filter, -1,1, TRUE));
		if(is_array($filter)){
			$output.=" AND ";
			foreach($filter as $key => $value){
				$output.="$key='%$value%'";
				if($key!=$last_key){
					$output.=" AND ";
				}
			}
		}

		if(!is_null($extra)){
			$output.=$extra;
		}

		$this->_query .= $output;
    }

    public function repair(){
		header("Content-type: text/plain");


		$this->_query = "SHOW TABLES";
		$this->query();

		foreach ($this->output->fetchAll() as $table) {

			$this->_query = "ALTER TABLE $table[0] DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci, CONVERT TO CHARACTER SET utf8";
			$this->pdo->exec($this->_query);

			$this->_query = "SHOW COLUMNS FROM $table[0]";
			$out = $this->pdo->query($this->_query);

			foreach($out->fetchAll() as $d){
				$this->_query = "ALTER TABLE $table[0]  ".$d['Field']."  ".$d['Field']." CHARACTER SET utf8 COLLATE utf8_turkish_ci";
				$this->pdo->exec($this->_query);
				$out2.=$table[0].".".$d['Field']." changed to UTF-8. <br>";
			}


			$out2.="$table[0] changed to UTF-8. <br>";
		}



		header("Content-type: text/html");
		return $out2;
 	}
  */

 	public function addPrimaryKey($key){
	 	$this->_query = "ALTER TABLE ".$this->_table." ADD PRIMARY KEY (".$key.")  ";
 	}

	private function is_iterable($var)
	{
			return $var !== null && (is_array($var) || $var instanceof Iterator || $var instanceof IteratorAggregate);
	}
	
	
	public function searchColumn($column=null){
		if(!is_null($column)){
			
			if(is_array($column)){
				$output1=null;
				$output2=null;
				$last_key=key(array_slice($column, -1,1, TRUE));
				foreach($column as $key => $val){
					$output1.="'$key'";
					$output2.="$key";
					if($key!=$last_key){
						$output1.=",";
						$output2.=",";
					}
				}
				
				
				$this->_query = 'SELECT DISTINCT TABLE_NAME 
		    					FROM INFORMATION_SCHEMA.COLUMNS
								WHERE COLUMN_NAME IN ('.$output1.')
								AND TABLE_SCHEMA=\''.$this->_config['database'].'\'';
			
			
				$this->query();
				
				$tables = $this->output->fetchAll();
				foreach($tables as $tb){
					
					$output=null;
					$last_key=key(array_slice($column, -1,1, TRUE));
					
					foreach($column as $key => $val){
						$output.="$key LIKE '%$val%'";
						if($key!=$last_key){
							$output.=" OR ";
						}
					}
					$this->_query = 'SELECT '.$output2.' FROM '.$tb['TABLE_NAME'];
					$this->_query.=" WHERE ".$output;
					
					
					$this->query();
					$find[$tb['TABLE_NAME']]=$this->output->fetchAll();
				}
				return $find;
			}else{
				$output='\''.$column.'\'';
				$this->_query = 'SELECT DISTINCT TABLE_NAME 
		    					FROM INFORMATION_SCHEMA.COLUMNS
								WHERE COLUMN_NAME IN ('.$output.')
								AND TABLE_SCHEMA=\''.$this->_config['database'].'\'';
			
			
				$this->query();
				
				$tables = $this->output->fetchAll();
				foreach($tables as $tb){
					$this->_query = 'SELECT '.$column.' FROM '.$tb['TABLE_NAME'];
					$this->query();
					$find[$tb['TABLE_NAME']]=$this->output->fetchAll();
					
				}
				return $find;
			}
			
			return false;
		}
		
	}
}
?>
