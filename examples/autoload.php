<?php

define('DATER_BASE_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Register Dater PSR-0 autoloader
 */
spl_autoload_register(function ($class) {
	if(strpos($class, 'Dater\\') === 0) {
		/** @noinspection PhpIncludeInspection */
		require_once(DATER_BASE_DIR . DIRECTORY_SEPARATOR . str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php');
	}
});
