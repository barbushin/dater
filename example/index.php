<?php

require_once(__DIR__ . '/../Dater/__autoload.php');

$dater = new Dater(new Dater_Locale_English());

echo '<pre>';


echo '<h2>Datetime source type</h2>';
$weekAgoTimestamp = time() - 60 * 60 * 24 * 7;
$weekAgoDateTime = date('Y-m-d H:i:s', $weekAgoTimestamp);
echo 'Server datetime (default = current): ' . $dater->serverDateTime() . PHP_EOL;
echo 'Server datetime by week ago timestamp: ' . $dater->serverDateTime($weekAgoTimestamp) . PHP_EOL;
echo 'Server datetime by week ago YYYY-MM-DD HH:II:SS: ' . $dater->serverDateTime($weekAgoDateTime) . PHP_EOL;


echo '<h2>Timezone and datetime in server format</h2>';

$dater->setServerTimezone('Europe/Moscow');
$dater->setClientTimezone('Europe/London');
echo 'Set server timezone: ' . $dater->getServerTimezone() . PHP_EOL;
echo 'Set client timezone: ' . $dater->getClientTimezone() . PHP_EOL;
echo 'Server date: ' . $dater->serverDate() . PHP_EOL;
echo 'Client date: ' . $dater->clientDate() . PHP_EOL;
echo 'Server time: ' . $dater->serverTime() . PHP_EOL;
echo 'Client time: ' . $dater->clientTime() . PHP_EOL;
echo 'Server datetime: ' . $dater->serverDateTime() . PHP_EOL;
echo 'Client datetime: ' . $dater->clientDateTime() . PHP_EOL;

$dater->setClientTimezone('Europe/Minsk');
echo 'Set client timezone: ' . $dater->getClientTimezone() . PHP_EOL;
echo 'Server datetime: ' . $dater->serverDateTime() . PHP_EOL;
echo 'Client datetime: ' . $dater->clientDateTime() . PHP_EOL;


echo '<h2>DateTime object init & format. Dater::modify() method</h2>';

$dateTime = $dater->initDateTime($weekAgoDateTime, 'Europe/London', 'UTC');
$dateTime->modify('+10 years');
echo 'Modified & formatted DateTime object: '. $dater->formatDateTime($dateTime, 'date') . PHP_EOL;
// or same thing in one line with Dater :)
echo 'Modify & format using Dater::modify(): '. $dater->modify($weekAgoDateTime, '+10 years', 'date', 'Europe/London', 'UTC');


echo '<h2>Locales</h2>';

$dater->setLocale(new Dater_Locale_English());
echo 'Set locale: : ' . get_class($dater->getLocale()) . PHP_EOL;
echo 'User date: ' . $dater->date() . PHP_EOL;
echo 'User formatted date: ' . $dater->now('j F Y') . PHP_EOL . PHP_EOL;

$dater->setLocale(Dater::getLocaleByCode('ru'));
echo 'Set locale: : ' . get_class($dater->getLocale()) . PHP_EOL;
echo 'User date: ' . $dater->date() . PHP_EOL;
echo 'User formatted date: ' . $dater->now('j F Y') . PHP_EOL . PHP_EOL;


echo '<h2>Formats binding</h2>';

echo 'User date: ' . $dater->date() . PHP_EOL;
echo 'Set "date" format as "d F Y"' . PHP_EOL;
$dater->setFormat(Dater::USER_DATE_FORMAT, 'd F Y');
echo 'User date: ' . $dater->date() . PHP_EOL;

echo 'Set new "week_date" format as "d F, D"' . PHP_EOL;
$dater->setFormat('week_date', 'd F, D');
echo 'User week_date: ' . $dater->format($weekAgoDateTime, 'week_date') . PHP_EOL;


echo '<h2>Custom format options</h2>';

echo 'Add new format option "ago" that will return date days left' . PHP_EOL;
$dater->addFormatOption('ago', function (DateTime $dateTime) {
	return floor((time() - $dateTime->getTimestamp()) / 86400) . ' days ago';
});
echo 'User week ago date in format "d F Y, ago": ' . $dater->format($weekAgoDateTime, 'd F Y, ago') . PHP_EOL;
echo 'So again, lets bind "d F Y, ago" format to alias "week_date_ago"' . PHP_EOL;
$dater->setFormat('week_date_ago', 'd F Y, ago');
echo 'User week ago date in format "week_date_ago": ' . $dater->format($weekAgoDateTime, 'week_date_ago') . PHP_EOL;


echo '<h2>Convert request datetime to server timezone</h2>';
echo 'Server timezone: ' . $dater->getServerTimezone() . PHP_EOL;
echo 'Client timezone: ' . $dater->getClientTimezone() . PHP_EOL;

$_GET = array('filter' => array('startsFrom' => $weekAgoDateTime));
$_POST = array('event' => array('starts' => $weekAgoDateTime));
$_REQUEST = array_merge($_GET, $_POST);

echo 'Client original request data: ' . PHP_EOL;
print_r($_GET);
print_r($_POST);
print_r($_REQUEST);

$daterDataHandler = new Dater_DataHandler($dater);
$daterDataHandler->convertRequestDataToServerTimezone();

echo 'Server timezone converted request data: ' . PHP_EOL;
print_r($_GET);
print_r($_POST);
print_r($_REQUEST);


echo '<h2>Convert text template datetime timezone</h2>';

$data = 'Timestamp format: ' . $weekAgoTimestamp . ' (will not be handled)
Timestamp format: ' . $weekAgoTimestamp . '[Y/m/d]
Timestamp format: ' . $weekAgoTimestamp . '[datetime]
Server datetime format: ' . $weekAgoDateTime . '[Y/m/d]
Server datetime format: ' . $weekAgoDateTime . '[time]
Server datetime format: ' . $weekAgoDateTime;

echo '<h3>Original data</h3>';
echo $data;

echo '<h3>Handled data</h3>';
echo $daterDataHandler->handleDataTimezone($data);


echo '<h2>Convert output datetime timezone</h2>';
echo '<h3>Original data</h3>';
echo $data;

echo '<h3>Handled output data</h3>';
$daterDataHandler->enableOutputTimezoneHandler();
echo $data;


echo '<h2>Auto-detect client timezone by JavaScript</h2>';
$timezoneDetector = new Dater_TimezoneDetector();
echo $timezoneDetector->getHtmlJsCode();
echo 'Detected client timezone: ' . $timezoneDetector->getClientTimezone();
