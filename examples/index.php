<?php

require_once(__DIR__ . '/autoload.php');

$dater = new Dater\Dater(new Dater\Locale\En());

echo '<pre>';
echo '<h2>Datetime source types</h2>';
$weekAgoTimestamp = time() - 60 * 60 * 24 * 7;
$weekAgoDatetime = date('Y-m-d H:i:s', $weekAgoTimestamp);
echo 'Server datetime (default = current): ' . $dater->serverDatetime() . PHP_EOL;
echo 'Server datetime by week ago timestamp: ' . $dater->serverDatetime($weekAgoTimestamp) . PHP_EOL;
echo 'Server datetime by week ago YYYY-MM-DD HH:II:SS: ' . $dater->serverDatetime($weekAgoDatetime) . PHP_EOL;

echo '<h2>Timezone and datetime in server format</h2>';

$dater->setServerTimezone('Europe/Moscow');
$dater->setClientTimezone('Europe/London');
echo 'Set server timezone: ' . $dater->getServerTimezone() . PHP_EOL;
echo 'Set client timezone: ' . $dater->getClientTimezone() . PHP_EOL;
echo 'Server date: ' . $dater->serverDate() . PHP_EOL;
echo 'Client date: ' . $dater->isoDate() . PHP_EOL;
echo 'Server time: ' . $dater->serverTime() . PHP_EOL;
echo 'Client time: ' . $dater->isoTime() . PHP_EOL;
echo 'Server datetime: ' . $dater->serverDatetime() . PHP_EOL;
echo 'Client datetime: ' . $dater->isoDatetime() . PHP_EOL;

$dater->setClientTimezone('Europe/Minsk');
echo 'Set client timezone: ' . $dater->getClientTimezone() . PHP_EOL;
echo 'Server datetime: ' . $dater->serverDatetime() . PHP_EOL;
echo 'Client datetime: ' . $dater->isoDatetime() . PHP_EOL;

echo '<h2>DateTime object init & format. Dater\Dater::modify() method</h2>';

$datetime = $dater->initDatetimeObject($weekAgoDatetime, 'Europe/London', 'UTC');
$datetime->modify('+10 years');
echo 'Modified & formatted DateTime object: ' . $dater->formatDatetimeObject($datetime, 'date') . PHP_EOL;
// or same thing in one line with Dater\Dater :)
echo 'Modify & format using Dater\Dater::modify(): ' . $dater->modify($weekAgoDatetime, '+10 years', 'date', 'Europe/London', 'UTC');

echo '<h2>Locales</h2>';

$dater->setLocale(new Dater\Locale\En());
echo 'Set locale: : ' . get_class($dater->getLocale()) . PHP_EOL;
echo 'User date: ' . $dater->date() . PHP_EOL;
echo 'User formatted date: ' . $dater->now('j F Y') . PHP_EOL . PHP_EOL;

$dater->setLocale(Dater\Dater::getLocaleByCode('ru'));
echo 'Set locale: : ' . get_class($dater->getLocale()) . PHP_EOL;
echo 'User date: ' . $dater->date() . PHP_EOL;
echo 'User formatted date: ' . $dater->now('j F Y') . PHP_EOL . PHP_EOL;

echo '<h2>Formats binding</h2>';

echo 'User date: ' . $dater->date() . PHP_EOL;
echo 'Set "date" format as "d F Y"' . PHP_EOL;
$dater->setFormat(Dater\Dater::USER_DATE_FORMAT, 'd F Y');
echo 'User date: ' . $dater->date() . PHP_EOL;

echo 'Set new "week_date" format as "d F, D"' . PHP_EOL;
$dater->setFormat('week_date', 'd F, D');
echo 'User week_date: ' . $dater->format($weekAgoDatetime, 'week_date') . PHP_EOL;

echo '<h2>Custom format options</h2>';

echo 'Add new format option "ago" that will return date days left' . PHP_EOL;
$dater->addFormatOption('ago', function (DateTime $datetime) {
	return floor((time() - $datetime->getTimestamp()) / 86400) . ' days ago';
});
echo 'User week ago date in format "d F Y, ago": ' . $dater->format($weekAgoDatetime, 'd F Y, ago') . PHP_EOL;
echo 'So again, lets bind "d F Y, ago" format to alias "week_date_ago"' . PHP_EOL;
$dater->setFormat('week_date_ago', 'd F Y, ago');
echo 'User week ago date in format "week_date_ago": ' . $dater->format($weekAgoDatetime, 'week_date_ago') . PHP_EOL;

echo '<h2>Convert request datetime to server timezone</h2>';
echo 'Server timezone: ' . $dater->getServerTimezone() . PHP_EOL;
echo 'Client timezone: ' . $dater->getClientTimezone() . PHP_EOL;

$_GET = array('filter' => array('startsFrom' => $weekAgoDatetime));
$_POST = array('event' => array('starts' => $weekAgoDatetime));
$_REQUEST = array_merge($_GET, $_POST);

echo 'Client original request data: ' . PHP_EOL;
print_r($_GET);
print_r($_POST);
print_r($_REQUEST);

$daterDataHandler = new Dater\DataHandler($dater);
$daterDataHandler->convertRequestDataToServerTimezone();

echo 'Server timezone converted request data: ' . PHP_EOL;
print_r($_GET);
print_r($_POST);
print_r($_REQUEST);

echo '<h2>Convert text template datetime timezone</h2>';

$data = 'Timestamp format: ' . $weekAgoTimestamp . ' (will not be handled)
Timestamp format: ' . $weekAgoTimestamp . '[Y/m/d]
Timestamp format: ' . $weekAgoTimestamp . '[datetime]
Server datetime format: ' . $weekAgoDatetime . '[Y/m/d]
Server datetime format: ' . $weekAgoDatetime . '[time]
Server datetime format: ' . $weekAgoDatetime;

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
$timezoneDetector = new Dater\TimezoneDetector();
echo $timezoneDetector->getHtmlJsCode();
echo 'Detected client timezone: ' . $timezoneDetector->getClientTimezone();
