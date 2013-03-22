<?php

/**
 * Datetime formats & timezones handler
 *
 * @see https://github.com/barbushin/dater
 * @author Sergey Barbushin http://linkedin.com/in/barbushin
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @copyright © Sergey Barbushin, 2013. Some rights reserved.
 */
class Dater {

	const USER_DATE_FORMAT = 'date';
	const USER_TIME_FORMAT = 'time';
	const USER_DATETIME_FORMAT = 'datetime';
	const SERVER_DATE_FORMAT = 'server_date';
	const SERVER_TIME_FORMAT = 'server_time';
	const SERVER_DATETIME_FORMAT = 'server_datetime';

	protected static $localesCodes = array(
		'en' => 'English',
		'ru' => 'Russian',
		'ua' => 'Ukrainian',
	);

	protected $formats = array(
		self::USER_DATE_FORMAT => 'm/d/Y',
		self::USER_TIME_FORMAT => 'g:i A',
		self::USER_DATETIME_FORMAT => 'm/d/Y g:i A',
		self::SERVER_DATE_FORMAT => 'Y-m-d',
		self::SERVER_TIME_FORMAT => 'H:i:s',
		self::SERVER_DATETIME_FORMAT => 'Y-m-d H:i:s',
	);

	/** @var Dater_Locale */
	protected $locale;
	/** @var DateTimezone[] */
	protected $timezonesObjects = array();
	protected $clientTimezone;
	protected $serverTimezone;
	protected $formatOptionsNames = array();
	protected $formatOptionsPlaceholders = array();
	protected $formatOptionsCallbacks = array();

	public function __construct(Dater_Locale $locale, $serverTimezone = null, $clientTimezone = null) {
		$this->setLocale($locale);
		$this->setServerTimezone($serverTimezone ? : date_default_timezone_get());
		$this->setClientTimezone($clientTimezone ? : $this->serverTimezone);
		$this->initCustomFormatOptions();
	}

	/**
	 * @return Dater_Locale
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * Get locale by 2-chars code: en, ru, ua
	 * @param $code
	 * @throws Exception
	 * @return
	 */
	public static function getLocaleByCode($code) {
		$code = strtolower($code);
		if(!isset(static::$localesCodes[$code])) {
			throw new Exception('Unknown locale code "' . $code . '". See available codes in Dater::$localeCodes.');
		}
		$class = 'Dater_Locale_' . static::$localesCodes[$code];
		return new $class();
	}

	public function setLocale(Dater_Locale $locale) {
		foreach($locale::$formats as $alias => $format) {
			$this->setFormat($alias, $format);
		}
		$this->locale = $locale;
	}

	protected function initCustomFormatOptions() {
		$dater = $this;
		$this->addFormatOption('F', function (DateTime $dateTime) use ($dater) {
			return $dater->getLocale()->getMonth($dateTime->format('n') - 1);
		});
		$this->addFormatOption('l', function (DateTime $dateTime) use ($dater) {
			return $dater->getLocale()->getMonth($dateTime->format('N') - 1);
		});
		$this->addFormatOption('D', function (DateTime $dateTime) use ($dater) {
			return $dater->getLocale()->getWeekDayShort($dateTime->format('N') - 1);
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
			throw new Exception('Argument $callback is not callable');
		}
		if(array_search($option, $this->formatOptionsPlaceholders) !== false) {
			throw new Exception('Option "' . $option . '" already added');
		}
		$this->formatOptionsNames[] = $option;
		$this->formatOptionsPlaceholders[] = '~' . count($this->formatOptionsPlaceholders) . '~';
		$this->formatOptionsCallbacks[] = $callback;
	}

	/**
	 * Stash custom format options from standard PHP DateTime format parser
	 * @param $format
	 * @return bool Return true if there was any custom options in $format
	 */
	protected function stashCustomFormatOptions(&$format) {
		$format = str_replace($this->formatOptionsNames, $this->formatOptionsPlaceholders, $format, $count);
		return (bool)$count;
	}

	/**
	 * Stash custom format options from standard PHP DateTime format parser
	 * @param $format
	 * @param DateTime $dateTime
	 * @return bool Return true if there was any custom options in $format
	 */
	protected function applyCustomFormatOptions(&$format, DateTime $dateTime) {
		$formatOptionsCallbacks = $this->formatOptionsCallbacks;
		$format = preg_replace_callback('/~(\d+)~/', function ($matches) use ($dateTime, $formatOptionsCallbacks) {
			return call_user_func($formatOptionsCallbacks[$matches[1]], $dateTime);
		}, $format);
	}

	/**
	 * Get date in Dater::$formats['date'] format, in client timezone
	 * @param string|int|null $serverDateTimeOrTimestamp Default value current timestamp
	 * @return string
	 */
	public function date($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, static::USER_DATE_FORMAT);
	}

	/**
	 * Get time in Dater::$timeFormat format, in client timezone
	 * @param string|int|null $serverDateTimeOrTimestamp Default value current timestamp
	 * @return string
	 */
	public function time($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, static::USER_TIME_FORMAT);
	}

	/**
	 * Get datetime in Dater::$formats[datetime] format, in client timezone
	 * @param string|int|null $serverDateTimeOrTimestamp Default value current timestamp
	 * @return string
	 */
	public function datetime($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, static::USER_DATETIME_FORMAT);
	}

	/**
	 * Format current datetime to specified format with timezone converting
	 * @param string|null $format http://php.net/date format or format FORMAT
	 * @param string|null $outputTimezone Default value is Dater::$clientTimezone
	 * @return string
	 */
	public function now($format, $outputTimezone = null) {
		return $this->format(null, $format, $outputTimezone);
	}

	/**
	 * Format date/datetime/timestamp to specified format with timezone converting
	 * @param string|int|null $dateTimeOrTimestamp Default value current timestamp
	 * @param string|null $format http://php.net/date format or format FORMAT
	 * @param string|null $outputTimezone Default value is Dater::$clientTimezone
	 * @param string|null $inputTimezone Default value is Dater::$serverTimezone
	 * @return string
	 */
	public function format($dateTimeOrTimestamp, $format, $outputTimezone = null, $inputTimezone = null) {
		$format = $this->getFormat($format) ? : $format;

		if(!$inputTimezone) {
			$inputTimezone = $this->serverTimezone;
		}
		if(!$outputTimezone) {
			$outputTimezone = $this->clientTimezone;
		}

		if(strlen($dateTimeOrTimestamp) == 10) {
			$isTimeStamp = is_numeric($dateTimeOrTimestamp);
			$isDate = !$isTimeStamp;
		}
		else {
			$isTimeStamp = false;
			$isDate = false;
		}

		if($isTimeStamp) {
			$dateTime = new DateTime();
			$dateTime->setTimestamp($dateTimeOrTimestamp);
		}
		else {
			$dateTime = new DateTime($dateTimeOrTimestamp, $inputTimezone ? $this->getTimezoneObject($inputTimezone) : null);
		}

		if(!$isDate && $outputTimezone && $outputTimezone != $inputTimezone) {
			$dateTime->setTimezone($this->getTimezoneObject($outputTimezone));
		}

		$isStashed = $this->stashCustomFormatOptions($format);
		$result = $dateTime->format($format);
		if($isStashed) {
			$this->applyCustomFormatOptions($result, $dateTime);
		}

		return $result;
	}

	/**
	 * Get date in YYYY-MM-DD format, in server timezone
	 * @param string|int|null $serverDateTimeOrTimestamp
	 * @return string
	 */
	function serverDate($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, self::SERVER_DATE_FORMAT, $this->serverTimezone);
	}

	/**
	 * Get date in HH-II-SS format, in server timezone
	 * @param string|int|null $serverDateTimeOrTimestamp
	 * @return string
	 */
	function serverTime($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, self::SERVER_TIME_FORMAT, $this->serverTimezone);
	}

	/**
	 * Get datetime in YYYY-MM-DD HH:II:SS format, in server timezone
	 * @param null $serverDateTimeOrTimestamp
	 * @return string
	 */
	function serverDateTime($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, self::SERVER_DATETIME_FORMAT, $this->serverTimezone);
	}

	/**
	 * Get date in YYYY-MM-DD format, in client timezone
	 * @param string|int|null $serverDateTimeOrTimestamp
	 * @return string
	 */
	function clientDate($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, self::SERVER_DATE_FORMAT);
	}

	/**
	 * Get date in HH-II-SS format, in client timezone
	 * @param string|int|null $serverDateTimeOrTimestamp
	 * @return string
	 */
	function clientTime($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, self::SERVER_TIME_FORMAT);
	}

	/**
	 * Get datetime in YYYY-MM-DD HH:II:SS format, in client timezone
	 * @param null $serverDateTimeOrTimestamp
	 * @return string
	 */
	function clientDateTime($serverDateTimeOrTimestamp = null) {
		return $this->format($serverDateTimeOrTimestamp, self::SERVER_DATETIME_FORMAT);
	}

	public function setFormat($alias, $format) {
		$this->formats[$alias] = $format;
	}

	protected function getFormat($alias) {
		if(isset($this->formats[$alias])) {
			return $this->formats[$alias];
		}
	}

	/**
	 * Get DateTimezone object by timezone name
	 * @param $timezone
	 * @return DateTimezone
	 */
	protected function getTimezoneObject($timezone) {
		if(!isset($this->timezonesObjects[$timezone])) {
			$this->timezonesObjects[$timezone] = new DateTimezone($timezone);
		}
		return $this->timezonesObjects[$timezone];
	}

	/**
	 * Magic call of $dater->formatFORMAT($dateTimeOrTimestamp). To annotate available formats-methods just add to class annotations:
	 *
	 * Example:
	 * @method string time $dateTimeOrTimestamp
	 *
	 * @param $formatFORMAT
	 * @param array $dateTimeArg
	 * @return string
	 * @throws Exception
	 */
	public function __call($formatFORMAT, array $dateTimeArg) {
		$format = $this->getFormat($formatFORMAT);
		if(!$format) {
			throw new Exception('There is no method or format FORMAT with name "' . $formatFORMAT . '"');
		}
		return $this->format(reset($dateTimeArg), $format);
	}
}

