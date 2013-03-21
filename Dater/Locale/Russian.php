<?php

class Dater_Locale_Russian extends Dater_Locale {

	public static $months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
	public static $weekDays = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
	public static $weekDaysShort = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');

	public static $formats = array(
		Dater::USER_DATE_FORMAT => 'd.m.Y',
		Dater::USER_TIME_FORMAT => 'h:i',
		Dater::USER_DATETIME_FORMAT => 'd.m.Y h:i',
	);
}
