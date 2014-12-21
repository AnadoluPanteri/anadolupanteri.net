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
		
		$this->view->set("navbar",'
		<!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/index.php/sayfa"><img style="display:inline;" width="48" src="/'.TEMPLATE_FOLDER."anasayfa/logo2.png".'">'.APPNAME.'</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                	<li class="sohbet">
                        <a href="https://anadolupanteri.slack.com/">Sohbet</a>
                    </li>
                	<li class="forum">
                        <a href="#">Forum</a>
                    </li>
                    <li class="takim">
                        <a href="/index.php/sayfa/takim">Takım</a>
                    </li>
                    <li class="iletisim">
                        <a href="/index.php/sayfa/iletisim">İletişim</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>');
	}
	function index(){
		$this->view->set("temp_dir","/".TEMPLATE_FOLDER."anasayfa");
		$this->view->load("index","anasayfa");
	}
	
	function takim(){
		$this->view->set("temp_dir","/".TEMPLATE_FOLDER."anasayfa");
		$this->view->load("takim","anasayfa");
	}
	
	function iletisim(){
		$this->view->set("temp_dir","/".TEMPLATE_FOLDER."anasayfa");
		$this->view->load("iletisim","anasayfa");
	}
	
	function construction(){
		$this->view->load("temp");
	}
}
?>
