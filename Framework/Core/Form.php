<?php
/**
 * Form class.
 */
class Form
{
	public $out;
	public $model;

	function __construct($model, $options=array(
			'class' => null,
			'id' => null,
			'action' => null,
			'method' => null,
			'enctype' => 'multipart/form-data'
		)){
		$this->out = '<form';
		@$this->out .= $options['class'] != null ? ' class="'.$options['class'].'"' : null;
		@$this->out .= $options['id'] != null ? ' id="'.$options['id'].'"' : null;
		@$this->out .= $options['action'] != null ? ' action="'.$options['action'].'"' : null;
		@$this->out .= $options['method'] != null ? ' method="'.$options['method'].'"' : null;
		@$this->out .= $options['enctype'] != null ? ' enctype="'.$options['enctype'].'"' : null;
		$this->out .= '>';
	}

	/**
	 * generate function.
	 *
	 * @access public
	 * @param array $fields (default: array())
	 * @return void
	 */
	function generate($fields=array()){
		foreach($fields as $field => $options){
			@$class = $options['class'] != null ? $options['class'] : null;
			@$id = $options['id'] != null ? $options['id'] : null;
			@$placeholder = $options['placeholder'] != null ? $options['placeholder'] : null;
			@$type = $options['options']['type'] != null ? $options['options']['type'] : null;
			@$onclick = $options['options']['onclick'] != null ? $options['options']['onclick'] : null;
			@$label = $options['label'] != null ? $options['label'] : null;
			@$html = $options['html'] != null ? $options['html'] : null;
			@$data = $options['data'] != null ? $options['data'] : null;
			@$value = $options['value'] != null ? $options['value'] : null;
			@$selected = $options['selected'] != null ? $options['selected'] : null;
			@$append = $options['append'] != null ? $options['append'] : null;
			@$content = $options['content'] != null ? $options['content'] : null;

			switch($options['type']){
			case 'input':
				$this->out.=$this->input(array(
						'class' => $class,
						'id' => $id,
						'name' => $field,
						'placeholder' => $placeholder,
						'type' => $type,
						'label' => $label,
						'value' => $value,
						'html' => $html,
						'append' => $append
					));
				break;
			case 'option':
				$this->out.=$this->option($data,array(
						'class' => $class,
						'id' => $id,
						'name' => $field,
						'data' => $data,
						'html' => $html,
						'label' => $label,
						'selected' => $selected,
						'append' => $append
					));
				break;

			case 'button':
				$this->out.=$this->button(array(
						'class' => $class,
						'id' => $id,
						'name' => $field,
						'value' => $value,
						'type' => $type,
						'onclick' => $onclick,
						'html' => $html,
						'append' => $append
					));
				break;

			case 'textarea':
				$this->out.=$this->textarea(array(
						'class' => $class,
						'id' => $id,
						'name' => $field,
						'label' => $label,
						'value' => $value,
						'html' => $html,
						'append' => $append
					));
				break;
			case 'label':
				$this->out.=$this->label(array(
						'class' => $class,
						'id' => $id,
						'append' => $append,
						'content' => $content,
						'html' => $html
					));
				break;
			}
		}
	}


	/**
	 * input function.
	 *
	 * @access public
	 * @param array $options (default: array())
	 * @return void
	 */
	function input($options=array()){
		@$out = $options['label'] != null ? "<label>".$options['label']."</label>" : null;
		@$out .= "<input";
		@$out .= $options['class'] != null ? ' class="'.$options['class'].'"' : null;
		@$out .= $options['id'] != null ? ' id="'.$options['id'].'"' : null;
		@$out .= $options['name'] != null ? ' name="'.$options['name'].'"' : null;
		@$out .= $options['type'] != null ? ' type="'.$options['type'].'"' : null;
		@$out .= $options['value'] != null ? ' value="'.$options['value'].'"' : null;
		@$out .= $options['placeholder'] != null ? ' placeholder="'.$options['placeholder'].'"' : null;
		$out .= " />";
		@$out .= $options['append'] != null ? $options['append'] : null;
		if($options['html']) $out = str_replace('%form%',$out,$options['html']);
		return $out;
	}

	/**
	 * option function.
	 *
	 * @access public
	 * @param array $array (default: array())
	 * @param array $options (default: array())
	 * @return void
	 */
	function option($array=array(),$options=array()){
		@$out = $options['label'] != null ? "<label>".$options['label']."</label>" : null;
		@$out .= "<select";
		@$out .= $options['class'] != null ? ' class="'.$options['class'].'"' : null;
		@$out .= $options['id'] != null ? ' id="'.$options['id'].'"' : null;
		@$out .= $options['name'] != null ? ' name="'.$options['name'].'"' : null;
		$out .= " >";
		@$out .= $options['append'] != null ? $options['append'] : null;
		if(is_array($array)){
			foreach($array as $key => $val){
				if(@$options['selected'] == $val){
					$selected = 'selected';
				}else $selected = null;

				$out .= "<option value='".$val."' ".$selected.">".$key."</option>";
			}
		}

		$out .= "</select>";
		if($options['html']) $out = str_replace('%form%',$out,$options['html']);
		return $out;
	}

	/**
	 * label function.
	 *
	 * @access public
	 * @param array $options (default: array())
	 * @return void
	 */
	function label($options=array()){
		$out = "<label";
		@$out .= $options['class'] != null ? ' class="'.$options['class'].'"' : null;
		@$out .= $options['id'] != null ? ' id="'.$options['id'].'"' : null;
		$out .= ">";
		@$out .= $options['content'] != null ? $options['content'] : null;
		$out .= "</label>";
		@$out .= $options['append'] != null ? $options['append'] : null;
		if($options['html']) $out = str_replace('%form%',$out,$options['html']);
		return $out;
	}

	/**
	 * button function.
	 *
	 * @access public
	 * @param array $options (default: array())
	 * @return void
	 */
	function button($options=array()){
		$out = "<button";
		@$out .= $options['class'] != null ? ' class="'.$options['class'].'"' : null;
		@$out .= $options['id'] != null ? ' id="'.$options['id'].'"' : null;
		//@$out .= $options['name'] != null ? ' name="'.$options['name'].'"' : null;
		@$out .= $options['type'] != null ? ' type="'.$options['type'].'"' : null;
		@$out .= $options['onclick'] != null ? ' onclick="'.$options['onclick'].'"' : null;
		$out .= ">";
		@$out .= $options['value'] != null ? $options['value'] : null;
		$out .= "</button>";
		@$out .= $options['append'] != null ? $options['append'] : null;
		if($options['html']) $out = str_replace('%form%',$out,$options['html']);
		return $out;
	}

	/**
	 * textarea function.
	 *
	 * @access public
	 * @param array $options (default: array())
	 * @return void
	 */
	function textarea($options=array()){
		$out = null;
		@$out .= $options['label'] != null ? "<label>".$options['label']."</label>" : null;
		@$out .= "<textarea";
		@$out .= $options['class'] != null ? ' class="'.$options['class'].'"' : null;
		@$out .= $options['id'] != null ? ' id="'.$options['id'].'"' : null;
		@$out .= $options['name'] != null ? ' name="'.$options['name'].'"' : null;
		$out .= ">";
		@$out .= $options['value'] != null ? $options['value'] : null;
		$out .= "</textarea>";
		@$out .= $options['append'] != null ? $options['append'] : null;
		if($options['html']) $out = str_replace('%form%',$out,$options['html']);
		return $out;
	}


	/**
	 * html function.
	 *
	 * @access public
	 * @param mixed $html
	 * @return void
	 */
	function html($html){
		$this->out .= $html;
	}

	/**
	 * end function.
	 *
	 * @access public
	 * @return void
	 */
	function end(){
		$this->out .= "</form>";
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */
	function output(){
		return $this->out;
	}
}
?>
