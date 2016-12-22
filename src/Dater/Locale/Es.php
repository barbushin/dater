<?php

namespace Dater\Locale;
use Dater\Dater;

class Es extends \Dater\Locale {

	protected static $months = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
	protected static $weekDays = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
	protected static $weekDaysShort = array('Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom');

	// Referring to https://en.wikipedia.org/wiki/Date_format_by_country
	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'd/m/Y',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'd/m/Y H:i',
	);
}
