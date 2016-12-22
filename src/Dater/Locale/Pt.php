<?php

namespace Dater\Locale;
use Dater\Dater;

class Pt extends \Dater\Locale {

	protected static $months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
	protected static $weekDays = array('Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo');
	protected static $weekDaysShort = array('Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom');

	// Referring to https://en.wikipedia.org/wiki/Date_format_by_country
	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'd/m/Y',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'd/m/Y H:i',
	);
}
