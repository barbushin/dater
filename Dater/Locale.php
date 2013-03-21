<?php

/**
 * Specify translations & formats for different locales
 *
 * @see https://github.com/barbushin/dater
 * @author Sergey Barbushin http://linkedin.com/in/barbushin
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @copyright © Sergey Barbushin, 2013. Some rights reserved.
 */
abstract class Dater_Locale {

	/** @var array */
	public static $months;
	/** @var array */
	public static $weekDays;
	/** @var array */
	public static $weekDaysShort;
	/** @var array */
	public static $formats;

	public static function getMonth($index) {
		return static::$months[$index];
	}

	public static function getWeekDay($index) {
		return static::$weekDays[$index];
	}

	public static function getWeekDayShort($index) {
		return static::$weekDaysShort[$index];
	}
}
