<?php

namespace Dater\Locale;
use Dater\Dater;

class Fr extends \Dater\Locale {

	protected static $months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	protected static $weekDays = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	protected static $weekDaysShort = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');

	// Referring to https://en.wikipedia.org/wiki/Date_format_by_country
	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'd-m-Y',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'd-m-Y H:i',
	);
}
