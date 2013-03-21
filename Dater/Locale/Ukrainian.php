<?php

class Dater_Locale_Ukrainian extends Dater_Locale {

    public static $months = array('січня', 'лютого', 'березня', 'квітня', 'травня', 'червня', 'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня');
    public static $weekDays = array('понеділок', 'вівторок', 'середа', 'четвер', "п'ятниця", 'субота', 'неділя');
    public static $weekDaysShort = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд');

    public static $formats = array(
        Dater::USER_DATE_FORMAT => 'd.m.Y',
        Dater::USER_TIME_FORMAT => 'h:i',
        Dater::USER_DATETIME_FORMAT => 'd.m.Y h:i',
    );
}
