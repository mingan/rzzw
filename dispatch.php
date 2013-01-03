<?php
spl_autoload_register(function ($class) {
	$class = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class);
	require_once __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
});

$params = array_intersect_key($_GET, array_flip(array('source', 'type', 'params')));

require_once('Routes.php');

$Dispatcher = new Dispatcher\Dispatcher();
$out = $Dispatcher->dispatch($params);
var_dump($out);