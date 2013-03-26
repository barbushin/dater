<?php

namespace Dater;

/**
 * Handle format & timezone of request/response data
 *
 * @see https://github.com/barbushin/dater
 * @author Sergey Barbushin http://linkedin.com/in/barbushin
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @copyright © Sergey Barbushin, 2013. Some rights reserved.
 */
class DataHandler {

	/** @var Dater */
	protected $dater;
	protected $outputTimezoneHandlerEnabled = false;

	/** @var array $matches[1] must contain datetime or timestamp; $matches[2] must contain format or be empty */
	public $dataDatetimeRegexps = array(
		'/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}|\d{10})\[(.*?)\]/',
		'/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})()([^\[]|$)/'
	);

	public function __construct(Dater $dater) {
		$this->dater = $dater;
	}

	/**
	 * Output data datetime will be handled by ob_start() & Dater\TimezoneDetector::handleDataTimezone()
	 */
	public function enableOutputTimezoneHandler() {
		if(!$this->outputTimezoneHandlerEnabled) {
			$this->outputTimezoneHandlerEnabled = true;
			ob_start();
		}
	}

	/**
	 * Convert all strings matched "YYYY-MM-DD HH:MM:SS" in $_POST, $_GET, $_REQUEST to Dater::$clientTimezone
	 */
	public function convertRequestDataToServerTimezone() {
		$vars = array(&$_POST, &$_GET, &$_REQUEST);
		foreach($vars as &$var) {
			$this->convertTimezoneRecursively($var);
		}
	}

	/**
	 * Recursively convert timezone for all array/object properties matched "YYYY-MM-DD HH:MM:SS"
	 * @param array|object $arrayOrObject
	 * @param string|null $outputTimezone Default value is Dater::$clientTimezone
	 * @param string|null $inputTimezone Default value is Dater::$serverTimezone
	 * @return mixed
	 * @throws \Exception
	 */
	public function convertTimezoneRecursively(&$arrayOrObject, $outputTimezone = null, $inputTimezone = null) {
		if(is_object($arrayOrObject)) {
			$arrayVar = array($arrayOrObject);
			return $this->convertTimezoneRecursively($arrayVar, $inputTimezone, $outputTimezone);
		}
		elseif(!is_array($arrayOrObject)) {
			throw new \Exception('Wrong type of data argument');
		}
		$dater = $this->dater;
		array_walk_recursive($arrayOrObject, function (&$var) use ($dater, $inputTimezone, $outputTimezone) {
			if(is_scalar($var)) {
				if(strlen($var) == 19 && preg_match('~^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$~', $var)) {
					$var = $dater->format($var, Dater::ISO_DATETIME_FORMAT, $outputTimezone, $inputTimezone);
				}
			}
			elseif(is_object($var)) {
				$propertiesValues = get_object_vars($var);
				$this->convertTimezoneRecursively($propertiesValues, $inputTimezone, $outputTimezone);
				foreach($propertiesValues as $property => $value) {
					$var->$property = $value;
				}
			}
		});
	}

	/**
	 * Convert datetime to specified format & client timezone. Converting patterns:
	 *  "YYYY-MM-DD HH:II:SS" strings to client timezone
	 *  "YYYY-MM-DD HH:II:SS[Y-m-d]" strings to client timezone in some format
	 *  "1363836570[Y-m-d]" timestamp strings to client timezone in some format
	 *
	 * @param $data
	 * @return mixed
	 */
	public function handleDataTimezone($data) {
		$dater = $this->dater;
		$data = preg_replace_callback($this->dataDatetimeRegexps, function ($matches) use ($dater) {
			if(!empty($matches[1])) {
				return $dater->format($matches[1], !empty($matches[2]) ? $matches[2] : Dater::ISO_DATETIME_FORMAT) . (isset($matches[3]) ? $matches[3] : '');
			}
		}, $data);
		return $data;
	}

	protected function handleOutputTimezone() {
		$data = ob_get_contents();
		ob_end_clean();
		echo $this->handleDataTimezone($data);
	}

	public function __destruct() {
		if($this->outputTimezoneHandlerEnabled) {
			$this->handleOutputTimezone();
		}
	}
}
