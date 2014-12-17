<?php
class Route
{
	public $root = array("home","construction");
	public $bind = array(
		"sayfa"=> array(
			'controller' => 'home',
			'default' => 'index',
			'custom' => false
		),
	);
}
?>
