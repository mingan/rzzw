<?php
spl_autoload_register(function ($class) {
	$class = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class);
	require_once __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
});

$params = array_intersect_key($_GET, array_flip(array('source', 'type', 'params')));

const CACHE_CHECK = false;
const CACHE_HOUR_LIMIT = 24;
const BASE = __DIR__;
const CACHE_DIR = 'cached';
require_once('Routes.php');

$Dispatcher = new Dispatcher\Dispatcher();
echo $Dispatcher->dispatch($params);