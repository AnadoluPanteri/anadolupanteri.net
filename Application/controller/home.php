<?php
class Home extends Controller
{
	function index(){
		$this->html = new HTML();
		$this->view->set("title",$this->html->title(APPNAME));
		$this->view->set("welcomeMessage","Anadolu Panteri");
		$this->view->partial("header","Application/template/default/header.html");
		$this->view->partial("footer","Application/template/default/footer.html");
		$this->view->set("temp_dir","Application/template/default");

		$this->view->load("temp");
	}
}
?>
