<?php

namespace Dater;

/**
 * Specify translations & formats for different locales
 *
 * @see https://github.com/barbushin/dater
 * @author Sergey Barbushin http://linkedin.com/in/barbushin
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @copyright © Sergey Barbushin, 2013. Some rights reserved.
 */
abstract class Locale {

	/** @var array */
	protected static $months;
	/** @var array */
	protected static $weekDays;
	/** @var array */
	protected static $weekDaysShort;
	/** @var array */
	protected static $formats;

	public static function getFormats() {
		return static::$formats;
	}

	public static function getMonth($index) {
		return static::$months[$index];
	}

	public static function getMonths() {
		return static::$months;
	}

	public static function getWeekDay($index) {
		return static::$weekDays[$index];
	}

	public static function getWeekDays() {
		return static::$weekDays;
	}

	public static function getWeekDayShort($index) {
		return static::$weekDaysShort[$index];
	}

	public static function getWeekDaysShort() {
		return static::$weekDaysShort;
	}
}
