<?php
define('BASEDIR',dirname("../index.php"));
require_once 'Framework/Core/Boot.php';
$boot = new Boot("Application/config/");
$boot->init();
?>
