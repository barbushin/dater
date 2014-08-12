<?php

namespace Dater;

/**
 * Datetime formats & timezones handler
 *
 * @see https://github.com/barbushin/dater
 * @author Sergey Barbushin http://linkedin.com/in/barbushin
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @copyright © Sergey Barbushin, 2013. Some rights reserved.
 *
 * All this methods works through Dater::__call method, mapped to format date with Dater::$formats[METHOD_NAME] format:
 * @method date($datetimeOrTimestamp = null) Get date in Dater::$formats['date'] format, in client timezone
 * @method time($datetimeOrTimestamp = null) Get date in Dater::$formats['time'] format, in client timezone
 * @method datetime($datetimeOrTimestamp = null) Get date in Dater::$formats['datetime'] format, in client timezone
 * @method isoDate($datetimeOrTimestamp = null) Get date in Dater::$formats['isoDate'] format, in client timezone
 * @method isoTime($datetimeOrTimestamp = null) Get date in Dater::$formats['isoTime'] format, in client timezone
 * @method isoDatetime($datetimeOrTimestamp = null) Get date in Dater::$formats['isoDatetime'] format, in client timezone
 */
class Dater {

	const USER_DATE_FORMAT = 'date';
	const USER_TIME_FORMAT = 'time';
	const USER_DATETIME_FORMAT = 'datetime';
	const ISO_DATE_FORMAT = 'isoDate';
	const ISO_TIME_FORMAT = 'isoTime';
	const ISO_DATETIME_FORMAT = 'isoDatetime';

	protected $formats = array(
		self::USER_DATE_FORMAT => 'm/d/Y',
		self::USER_TIME_FORMAT => 'g:i A',
		self::USER_DATETIME_FORMAT => 'm/d/Y g:i A',
		self::ISO_DATE_FORMAT => 'Y-m-d',
		self::ISO_TIME_FORMAT => 'H:i:s',
		self::ISO_DATETIME_FORMAT => 'Y-m-d H:i:s',
	);

	/** @var Locale */
	protected $locale;
	/** @var \DateTimezone[] */
	protected $timezonesObjects = array();
	protected $clientTimezone;
	protected $serverTimezone;
	protected $formatOptionsNames = array();
	protected $formatOptionsPlaceholders = array();
	protected $formatOptionsCallbacks = array();

	public function __construct(Locale $locale, $serverTimezone = null, $clientTimezone = null) {
		$this->setLocale($locale);
		$this->setServerTimezone($serverTimezone ? : date_default_timezone_get());
		$this->setClientTimezone($clientTimezone ? : $this->serverTimezone);
		$this->initCustomFormatOptions();
	}

	/**
	 * @return Locale
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * Get locale by language & country code. See available locales in /Dater/Locale/*
	 * @param string $languageCode
	 * @param null $countryCode
	 * @throws \Exception
	 * @return
	 */
	public static function getLocaleByCode($languageCode, $countryCode = null) {
		$class = 'Dater\Locale\\' . ucfirst(strtolower($languageCode)) . ($countryCode ? ucfirst(strtolower($countryCode)) : '');
		if(!class_exists($class)) {
			throw new \Exception('Unknown locale code. Class "' . $class . '" not found.');
		}
		return new $class();
	}

	public function setLocale(Locale $locale) {
		foreach($locale::getFormats() as $alias => $format) {
			$this->setFormat($alias, $format);
		}
		$this->locale = $locale;
	}

	protected function initCustomFormatOptions() {
		$dater = $this;
		$this->addFormatOption('F', function (\DateTime $datetime) use ($dater) {
			return $dater->getLocale()->getMonth($datetime->format('n') - 1);
		});
		$this->addFormatOption('l', function (\DateTime $datetime) use ($dater) {
			return $dater->getLocale()->getWeekDay($datetime->format('N') - 1);
		});
		$this->addFormatOption('D', function (\DateTime $datetime) use ($dater) {
			return $dater->getLocale()->getWeekDayShort($datetime->format('N') - 1);
		});
	}

	public function setServerTimezone($timezone, $setSystemGlobal = true) {
		if($setSystemGlobal) {
			date_default_timezone_set($timezone);
		}
		$this->serverTimezone = $timezone;
	}

	public function getServerTimezone() {
		return $this->serverTimezone;
	}

	public function setClientTimezone($timezone) {
		$this->clientTimezone = $timezone;
	}

	public function getClientTimezone() {
		return $this->clientTimezone;
	}

	public function addFormatOption($option, $callback) {
		if(!is_callable($callback)) {
			throw new \Exception('Argument $callback is not callable');
		}
		if(array_search($option, $this->formatOptionsPlaceholders) !== false) {
			throw new \Exception('Option "' . $option . '" already added');
		}
		$this->formatOptionsNames[] = $option;
		$this->formatOptionsPlaceholders[] = '~' . count($this->formatOptionsPlaceholders) . '~';
		$this->formatOptionsCallbacks[] = $callback;
	}

	/**
	 * Stash custom format options from standard PHP \DateTime format parser
	 * @param $format
	 * @return bool Return true if there was any custom options in $format
	 */
	protected function stashCustomFormatOptions(&$format) {
		$format = str_replace($this->formatOptionsNames, $this->formatOptionsPlaceholders, $format, $count);
		return (bool)$count;
	}

	/**
	 * Stash custom format options from standard PHP \DateTime format parser
	 * @param $format
	 * @param \DateTime $datetime
	 * @return bool Return true if there was any custom options in $format
	 */
	protected function applyCustomFormatOptions(&$format, \DateTime $datetime) {
		$formatOptionsCallbacks = $this->formatOptionsCallbacks;
		$format = preg_replace_callback('/~(\d+)~/', function ($matches) use ($datetime, $formatOptionsCallbacks) {
			return call_user_func($formatOptionsCallbacks[$matches[1]], $datetime);
		}, $format);
	}

	/**
	 * Format current datetime to specified format with timezone converting
	 * @param string|null $format http://php.net/date format or format name
	 * @param string|null $outputTimezone Default value is Dater::$clientTimezone
	 * @return string
	 */
	public function now($format, $outputTimezone = null) {
		return $this->format(null, $format, $outputTimezone);
	}

	/**
	 * Init standard \DateTime object configured to outputTimezone corresponding to inputTimezone
	 * @param null $datetimeOrTimestamp
	 * @param null $inputTimezone
	 * @param null $outputTimezone
	 * @return \DateTime
	 */
	public function initDatetimeObject($datetimeOrTimestamp = null, $inputTimezone = null, $outputTimezone = null) {
		if(!$inputTimezone) {
			$inputTimezone = $this->serverTimezone;
		}
		if(!$outputTimezone) {
			$outputTimezone = $this->clientTimezone;
		}

		if(strlen($datetimeOrTimestamp) == 10) {
			$isTimeStamp = is_numeric($datetimeOrTimestamp);
			$isDate = !$isTimeStamp;
		}
		else {
			$isTimeStamp = false;
			$isDate = false;
		}

		if($isTimeStamp) {
			$datetime = new \DateTime();
			$datetime->setTimestamp($datetimeOrTimestamp);
		}
		else {
			$datetime = new \DateTime($datetimeOrTimestamp, $inputTimezone ? $this->getTimezoneObject($inputTimezone) : null);
		}

		if(!$isDate && $outputTimezone && $outputTimezone != $inputTimezone) {
			$datetime->setTimezone($this->getTimezoneObject($outputTimezone));
		}
		return $datetime;
	}

	/**
	 * Format \DateTime object to http://php.net/date format or format name
	 * @param \DateTime $datetime
	 * @param $format
	 * @return string
	 */
	public function formatDatetimeObject(\DateTime $datetime, $format) {
		$format = $this->getFormat($format) ? : $format;
		$isStashed = $this->stashCustomFormatOptions($format);
		$result = $datetime->format($format);
		if($isStashed) {
			$this->applyCustomFormatOptions($result, $datetime);
		}
		return $result;
	}

	/**
	 * Format date/datetime/timestamp to specified format with timezone converting
	 * @param string|int|null $datetimeOrTimestamp Default value current timestamp
	 * @param string|null $format http://php.net/date format or format name. Default value is current
	 * @param string|null $outputTimezone Default value is Dater::$clientTimezone
	 * @param string|null $inputTimezone Default value is Dater::$serverTimezone
	 * @return string
	 */
	public function format($datetimeOrTimestamp, $format, $outputTimezone = null, $inputTimezone = null) {
		$datetime = $this->initDatetimeObject($datetimeOrTimestamp, $inputTimezone, $outputTimezone);
		$result = $this->formatDatetimeObject($datetime, $format);
		return $result;
	}

	/**
	 * @param $datetimeOrTimestamp
	 * @param string $modify Modification string as in http://php.net/date_modify
	 * @param string|null $format http://php.net/date format or format name. Default value is Dater::ISO_DATETIME_FORMAT
	 * @param string|null $outputTimezone Default value is Dater::$serverTimezone
	 * @param string|null $inputTimezone Default value is Dater::$serverTimezone
	 * @return string
	 */
	public function modify($datetimeOrTimestamp, $modify, $format = null, $outputTimezone = null, $inputTimezone = null) {
		$format = $format ? : self::ISO_DATETIME_FORMAT;
		$outputTimezone = $outputTimezone ? : $this->serverTimezone;
		$inputTimezone = $inputTimezone ? : $this->serverTimezone;
		$datetime = $this->initDatetimeObject($datetimeOrTimestamp, $inputTimezone, $outputTimezone);
		$datetime->modify($modify);
		return $this->formatDatetimeObject($datetime, $format);
	}

	/**
	 * Get date in YYYY-MM-DD format, in server timezone
	 * @param string|int|null $serverDatetimeOrTimestamp
	 * @return string
	 */
	public function serverDate($serverDatetimeOrTimestamp = null) {
		return $this->format($serverDatetimeOrTimestamp, self::ISO_DATE_FORMAT, $this->serverTimezone);
	}

	/**
	 * Get date in HH-II-SS format, in server timezone
	 * @param string|int|null $serverDatetimeOrTimestamp
	 * @return string
	 */
	public function serverTime($serverDatetimeOrTimestamp = null) {
		return $this->format($serverDatetimeOrTimestamp, self::ISO_TIME_FORMAT, $this->serverTimezone);
	}

	/**
	 * Get datetime in YYYY-MM-DD HH:II:SS format, in server timezone
	 * @param null $serverDatetimeOrTimestamp
	 * @return string
	 */
	public function serverDatetime($serverDatetimeOrTimestamp = null) {
		return $this->format($serverDatetimeOrTimestamp, self::ISO_DATETIME_FORMAT, $this->serverTimezone);
	}

	public function setFormat($alias, $format) {
		$this->formats[$alias] = $format;
	}

	/**
	 * @param $alias
	 * @return string|null
	 */
	public function getFormat($alias) {
		if(isset($this->formats[$alias])) {
			return $this->formats[$alias];
		}
	}

	/**
	 * @return array
	 */
	public function getFormats() {
		return $this->formats;
	}

	/**
	 * Get Datetimezone object by timezone name
	 * @param $timezone
	 * @return \DateTimezone
	 */
	protected function getTimezoneObject($timezone) {
		if(!isset($this->timezonesObjects[$timezone])) {
			$this->timezonesObjects[$timezone] = new \DateTimezone($timezone);
		}
		return $this->timezonesObjects[$timezone];
	}

	/**
	 * Magic call of $dater->format($datetimeOrTimestamp, $formatAlias).
	 *
	 * Example:
	 *   $dater->setFormat('shortDate', 'd/m')
	 *   echo $dater->shortDate(time());
	 * To annotate available formats-methods just add to Dater class annotations like:
	 *   @method shortDate($datetimeOrTimestamp = null)
	 *
	 * @param $formatAlias
	 * @param array $datetimeOrTimestampArg
	 * @return string
	 * @throws \Exception
	 */
	public function __call($formatAlias, array $datetimeOrTimestampArg) {
		$formatAlias = $this->getFormat($formatAlias);
		if(!$formatAlias) {
			throw new \Exception('There is no method or format with name "' . $formatAlias . '"');
		}
		return $this->format(reset($datetimeOrTimestampArg), $formatAlias);
	}
}
