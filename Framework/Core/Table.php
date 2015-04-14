<?php
/**
 * Table class.
 */
namespace Bluejacket; 
class Table
{
	public $model;
	public $out=null;


	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $model
	 * @param array $options (default: array())
	 * @return void
	 */
	function __construct($model, $options=array()){
		$error = new Error();
		if($error->checkClass($model)){
			$this->model = new $model();
		}
		


		if(isset($options["search"]) && is_array($options["search"])){
			foreach ($this->model->search() as $key) {
				$sq[$key] = $options["search"][0];
			}
			$this->model->db->search($sq,$options["search"][1]);
			$this->model->db->query();
			if($this->model->db->output){
				$this->arr = $this->model->db->output->fetchAll();
				$this->count = count($this->arr);
			}else{
				$this->model->db->select();
				$this->model->db->query();
				$this->arr = $this->model->db->output->fetchAll();
				$this->count = $this->model->count();
			}
		}else{
			$this->model->db->select();
			if(isset($options["where"]) && is_array($options["where"])) $this->model->db->where($options["where"]);
			if(isset($options["orderby"]) && is_array($options["orderby"])) $this->model->db->orderBy($options["orderby"][0],$options["orderby"][1]);
			if(isset($options["groupby"]) && isset($options["groupby"])) $this->model->db->groupBy($options["groupby"]);
			if(isset($options["limit"]) && is_array($options["limit"])){
				$this->start = $options["limit"][0];
				$this->model->db->limit($options["limit"][0],$options["limit"][1]);
			}
			$this->model->db->query();
			$this->arr = $this->model->db->output->fetchAll();
			$this->count = $this->model->count();
		}

	}



	/**
	 * generate function.
	 * 
	 * @access public
	 * @param mixed $headers (default: null)
	 * @param mixed $class (default: null)
	 * @param mixed $id (default: null)
	 * @param mixed $actions (default: null)
	 * @param mixed $links (default: null)
	 * @param bool $showId (default: false)
	 * @param array $error (default: array())
	 * @return void
	 */
	public function generate($headers=null,$class=null,$id=null,$actions=null,$links=null,$showId=false,$error=array()){

		if($this->model->db->output){
			$this->out .= "<table";
			$this->out .= $class != null ? " class='".$class."'" : null;
			$this->out .= $id != null ? " id='".$id."'" : null;
			$this->out .= ">";

			if(count($this->arr)==0){
				if(is_array($error)){
					$this->out = "<div ".($error['class'] != null ? "class=\"".$error['class']."\"" : null)." ".($error['id'] != null ? "id=\"".$error['id']."\"" : null).">".($error['content'] != null ? $error['content'] : null)."</div>";
					$this->error = true;
					return;
				}
			}

			if($headers != null && is_array($headers)){
				$this->out .= "<thead><tr>";
				if($showId){
					$this->out.="<th class='row id'>#</th>";
				}
				foreach($headers as $row => $customname){
					$this->out .= "<th>".$customname."</th>";
				}



				if($actions != null) $this->out .= "<th class='row actions'></th>";
				$this->out .= "</tr></thead>";
			}

			$primaryKey = $this->model->getPrimaryKey();
			$arr = $this->arr;

			/* if($actions != null) $this->out .= "<th>".$actions."</th>"; */
			$this->out .= "<tbody>";
			$list = array();
			$i=0;
			while($i<count($arr)){
				$this->out.= "<tr>";
				if($showId){
					$this->out.="<td class='row id id-".($i+1+$this->start)."'>".($i+1+$this->start)."</td>";
				}
				if($headers != null && is_array($headers)){
					foreach($headers as $row => $customname){
						if($links != null && is_array($links)){
							foreach($links as $rw => $opts){
								if($rw == $row){
									$submodel = new $opts['model'];
									$submodel->get(array($opts['extract'] => $arr[$i][$opts['value']]));

									if(@is_numeric($submodel->id)){
										$newurl = str_replace("%model%",$opts['model'],$opts['url']);
										$newurl = str_replace("%id%",$submodel->id,$newurl);
										$newurl = str_replace("%extract%",$submodel->$opts['extract'],$newurl);
										$this->out.="<td><a href=".$newurl.">".$submodel->$opts['output']."</a></td>";
									}else if(isset($opts['custom'])){
											$this->out.="<td>".str_replace("%primaryKey%",$arr[$i][$primaryKey],$opts['custom'])."</td>";
										}else{
										$this->out.="<td>".$arr[$i][$row]."</td>";
									}

									$list[] = $row;
								}
							}
						}
						if(!in_array($row,$list)){
							$this->out.="<td>".$arr[$i][$row]."</td>";
						}
					}

					if($actions != null) $this->out .= "<td>".str_replace("%primaryKey%",$arr[$i][$primaryKey],$actions)."</td>";



					$this->out.= "</tr>";

				}
				$i++;
			}
			$this->out .= "</tbody></table>";
		}else{
			if(is_array($error)){
				$this->out = "<div ".($error['class'] != null ? "class=\"".$error['class']."\"" : null)." ".($error['id'] != null ? "id=\"".$error['id']."\"" : null).">".($error['content'] != null ? $error['content'] : null)."</div>";
				$this->error = true;
			}
		}

	}

	/**
	 * pagination function.
	 *
	 * @access public
	 * @param mixed $options (array)
	 * @return void
	 */
	public function pagination($options=array(
			"button" => array(
				"class" => null,
				"id" => null,
				"reverse" => null
			),
			"link" => array(
				"class"=> null,
				"id"=> null,
				"reverse" => null
			),
			"html" => null,
			"url" => null,
			"cutLine" => null,
			"count" => null,
			"prev" => null,
			"next" => null
		)){

		$count = $this->count;
		@$slice = $count/$options['count'];
		@$slice = ceil($slice);
		@$mod = $count % $options['count'];
		if($slice >= 1){
			$button = null;

			if(isset($_GET['page'])) $page = $_GET['page'];
			else $page = 1;

			$prev = $page-1;
			$next = $page+1;


			if(@$options['prev'] != null && $prev > 0){
				$button .= "<div ";
				$button .= isset($options['button']['class']) && $options['button']['class'] != null ? " class='".$options['button']['class']."'" : null;
				$button .= isset($options['button']['id']) && $options['button']['id'] != null ? " id='".$options['button']['class']."'" : null;
				$button .="><a href=";
				$button .= str_replace("%page%",$prev,$options['url']);
				$button .= isset($options['link']['class']) && $options['link']['class'] != null ? " class='".$options['link']['class']."'" : null;
				$button .= isset($options['link']['id']) && $options['link']['id'] != null ? " id='".$options['link']['class']."'" : null;
				$button .=">".$options['prev']."</a></div>";
			}

			$i=1;
			while($i<=$slice){
				if(@$page){
					@$button_reverse = $_GET['page']==$i ? $options['button']['reverse'] : null;
					@$lnk_reverse = $_GET['page']==$i ? $options['link']['reverse'] : null;
				}else{
					@$button_reverse = $i==1 ? $options['button']['reverse'] : null;
					@$lnk_reverse = $i==1 ? $options['link']['reverse'] : null;
				}

				$button .= "<div ";
				$button .= isset($options['button']['class']) && $options['button']['class'] != null ? " class='".$options['button']['class'].$button_reverse."'" : null;
				$button .= isset($options['button']['id']) && $options['button']['id'] != null ? " id='".$options['button']['class']."'" : null;
				$button .="><a href=";
				$button .= str_replace("%page%",$i,$options['url']);
				$button .= isset($options['link']['class']) && $options['link']['class'] != null ? " class='".$options['link']['class'].$lnk_reverse."'" : null;
				$button .= isset($options['link']['id']) && $options['link']['id'] != null ? " id='".$options['link']['class']."'" : null;
				$button .=">".$i."</a></div>";
				$i++;
			}

			if(@$options['next'] != null && $slice != $page){
				$button .= "<div ";
				$button .= isset($options['button']['class']) && $options['button']['class'] != null ? " class='".$options['button']['class']."'" : null;
				$button .= isset($options['button']['id']) && $options['button']['id'] != null ? " id='".$options['button']['class']."'" : null;
				$button .="><a href=";
				@$button .= str_replace("%page%",$next,$options['url']);
				$button .= isset($options['link']['class']) && $options['link']['class'] != null ? " class='".$options['link']['class']."'" : null;
				$button .= isset($options['link']['id']) && $options['link']['id'] != null ? " id='".$options['link']['class']."'" : null;
				$button .=">".$options['next']."</div>";
			}

			$this->out .= str_replace("%buttons%",$button,$options['html']);
			return str_replace("%buttons%",$button,$options['html']);
		}
	}


	/**
	 * html function.
	 *
	 * @access public
	 * @param mixed $html
	 * @return void
	 */
	public function html($html){
		$this->out.=$html;
	}


	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */
	public function output(){
		return $this->out;
	}
}
?>
