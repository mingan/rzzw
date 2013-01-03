<?php
spl_autoload_register(function ($class) {
	$class = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class);
	require_once __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
});

$FB = new Fetchers\Freebase();

var_dump($FB->fetch());