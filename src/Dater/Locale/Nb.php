<?php

namespace Dater\Locale;
use Dater\Dater;

class Nb extends \Dater\Locale {

	protected static $months = array('Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember');
	protected static $weekDays = array('Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag', 'Søndag');
	protected static $weekDaysShort = array('Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør', 'Søn');

	// Referring to https://en.wikipedia.org/wiki/Date_format_by_country
	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'd.m.Y',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'd.m.Y H:i',
	);
}
