<?php

namespace Dater\Locale;
use Dater\Dater;

class Ru extends \Dater\Locale {

	protected static $months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	protected static $weekDays = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
	protected static $weekDaysShort = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');

	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'd.m.Y',
		Dater::USER_TIME_FORMAT => 'G:i',
		Dater::USER_DATETIME_FORMAT => 'd.m.Y G:i',
	);
}
