<?php
class Table
{
  public $model;
  public $out;
  /*
    Example;
    $options=array(
      "where" => array("exampleRow"=>"exampleData"),
      "orderby" => array("exampleRow",true|false), -> if(asc) true|false
      "limit" => array(0,100)
      "groupby" => array("exampleRow",true|false) -> if(asc) true|false
    )
  */
  function __construct($model, $options=array(
    "where" => null,
    "orderby" => null,
    "limit" => null,
    "groupby" => null,
    "search" => array(
    	null,
    	array(
			"regexp" => false,
			"filter" => null,
			"extra" => null,
			"or" => false
		))
  )){
    $this->model = new $model();
    
    foreach($options as $option => $content){
	  if($option == "search" && isset($content) && is_array($content)){
		  foreach ($this->model->search() as $key) {
				$sq[$key] = $content[0];
			}
		  $this->model->db->search($sq,$content[1]);
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
		  if($option == "where" && isset($content) && is_array($content)) $this->model->db->where($content);
	      if($option == "orderby" && isset($content) && is_array($content)) $this->model->db->orderBy($content[0],$content[1]);
	      if($option == "groupby" && isset($content) && is_array($content)) $this->model->db->groupBy($content[0],$content[1]);
	      if($option == "limit" && isset($content) && is_array($content)){
	        $this->start = $content[0];
	        $this->model->db->limit($content[0],$content[1]);
	      }
		  $this->model->db->query();
		  $this->arr = $this->model->db->output->fetchAll();
		  $this->count = $this->model->count();	
	  }
    }
  }

  /*
   $class -> is table class
   $id -> is table id
   $actions -> custom html actions (end of table row)
   $links=array(
    "row" => array(
      "model" => "modelname",
      "extract" => "id",
      "output" => "name",
      "url" => "url"
      )
   )

   $headers = array(
    "tablerowname" => "customname"
   )
  */
  public function generate($headers=null,$class=null,$id=null,$actions=null,$links=null,$showId=false,$error=array(
		"class" => null,
		"id" => null,
		"content" => null
	)){
    

    
    if($this->model->db->output){
		$this->out = "<table";
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
                    $submodel->get(array($opts['extract'] => $arr[$i][$row]));
                    
                    if(@is_numeric($submodel->id)){
	                    $newurl = str_replace("%model%",$opts['model'],$opts['url']);
	                    $newurl = str_replace("%id%",$submodel->id,$newurl);
	                    $newurl = str_replace("%extract%",$submodel->$opts['extract'],$newurl);   
	                    $this->out.="<td><a href=".$newurl.">".$submodel->$opts['output']."</a></td>";
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
    @$slice = round($count/$options['count']);
	@$mod = $count % $options['count'];
	if($slice > 1){
		
	
	    if($mod > 0){
	      $slice = $slice + 1;
	    }
	
	    $button = null;
	
	    if(isset($_GET['page']) && $_GET['page'] > 0) $prev = $_GET['page']-1;
	    if(isset($_GET['page']) && $_GET['page'] > 0) $next = $_GET['page']+1;
	
	    if(@$options['prev'] != null && @$_GET['page'] > 0){
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
	      if(@$_GET['page']){
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
	
	    if(@$options['next'] != null && $slice != @$_GET['page']){
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
	}
  }


  public function output(){
    return $this->out;
  }
}
?>
