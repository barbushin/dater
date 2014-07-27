<?php

namespace Dater\Locale;
use Dater\Dater;

class De extends \Dater\Locale {

	protected static $months = array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
	protected static $weekDays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
	protected static $weekDaysShort = array('Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So');

	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'j F Y',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'd.m.Y H:i',
	);
}
