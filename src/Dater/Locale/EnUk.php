<?php

namespace Dater\Locale;
use Dater\Dater;

class EnUk extends \Dater\Locale {

	protected static $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	protected static $weekDays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	protected static $weekDaysShort = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'm/d/Y',
		Dater::USER_TIME_FORMAT => 'G:i',
		Dater::USER_DATETIME_FORMAT => 'm/d/Y G:i',
	);
}
