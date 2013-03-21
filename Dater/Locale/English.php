<?php

class Dater_Locale_English extends Dater_Locale {

	public static $months = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	public static $weekDays = array('January', 'February', 'March', 'April', 'May', 'June', 'Jule', 'August', 'September', 'October', 'November', 'December');
	public static $weekDaysShort = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

	public static $formats = array(
		Dater::USER_DATE_FORMAT => 'm/d/Y',
		Dater::USER_TIME_FORMAT => 'g:i A',
		Dater::USER_DATETIME_FORMAT => 'm/d/Y g:i A',
	);
}
