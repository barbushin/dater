<?php

namespace Dater\Locale;
use Dater\Dater;

class Tr extends \Dater\Locale {

	protected static $months = array('Ocak ayı', 'Şubat ayı', 'Mart', 'Nisan', 'Mayıs ayı', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
	protected static $weekDays = array('Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar');
	protected static $weekDaysShort = array('Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct', 'Pz');

	// Referring to https://en.wikipedia.org/wiki/Date_format_by_country
	protected static $formats = array(
		Dater::USER_DATE_FORMAT => 'd.m.Y',
		Dater::USER_TIME_FORMAT => 'H:i',
		Dater::USER_DATETIME_FORMAT => 'd.m.Y H:i',
	);
}
