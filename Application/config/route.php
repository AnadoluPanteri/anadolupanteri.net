<?php
class Route
{
	public $root = array("home","index");
	public $bind = array(
		"sayfa"=> array(
			'controller' => 'home',
			'default' => 'index',
			'custom' => false
		),
	);
}
?>
