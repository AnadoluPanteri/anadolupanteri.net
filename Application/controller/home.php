<?php
class Home extends Controller
{
	function __construct(){
		parent::__construct();
		$this->html = new HTML();
		$this->view->set("title",$this->html->title(APPNAME));
		$this->view->partial("header",TEMPLATE_FOLDER."default/header.html");
		$this->view->partial("footer",TEMPLATE_FOLDER."default/footer.html");
		$this->view->set("temp_dir",TEMPLATE_FOLDER."default");
	}
	function index(){
		$this->view->load("index","anasayfa");
	}
	
	
	function construction(){
		$this->view->load("temp");
	}
}
?>
