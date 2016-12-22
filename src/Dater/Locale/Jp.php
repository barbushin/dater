<?php

namespace Dater\Locale;
use Dater\Dater;

class Jp extends \Dater\Locale {

	protected static $months = array('1月', '2月', '行進', '4月', '5月', '六月', '7月', '8月', '9月', '10月', '11月', '12月');
	protected static $weekDays = array('月曜', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日');
	protected static $weekDaysShort = array('月曜', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日');

	// Referring to https://en.wikipedia.org/wiki/Date_format_by_country
	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'Y年m月d日',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'Y年m月d日 H:i',
	);
}
