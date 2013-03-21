<?php

spl_autoload_register(function ($class) {
	if(strpos($class, 'Dater') === 0) {
		/** @noinspection PhpIncludeInspection */
		require_once(__DIR__ . DIRECTORY_SEPARATOR . str_replace(array('Dater_', '_'), array('', DIRECTORY_SEPARATOR), $class) . '.php');
	}
});
