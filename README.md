# Dater

[![Author](http://img.shields.io/badge/author-@barbushin-blue.svg?style=flat-square)](https://www.linkedin.com/in/barbushin)
[![GitHub release](https://img.shields.io/github/release/barbushin/dater.svg?maxAge=2592000&style=flat-square)](https://packagist.org/packages/dater/dater)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist](https://img.shields.io/packagist/dt/dater/dater.svg?maxAge=86400&style=flat-square)](https://packagist.org/packages/dater/dater)

This library can be very helpful to standardize date-time formats in your project & get done easy with different clients timezones.

# Install

The recommended way to install Dater is through [Composer](http://getcomposer.org).
You can see [package information on Packagist.](https://packagist.org/packages/dater/dater)

	{
		"require": {
			"dater/dater": "^2.0"
		}
	}

# Features

For all features using examples see [/example/index.php](https://github.com/barbushin/dater/blob/master/examples/index.php). PHP > 5.3 required.

### Timestamp/datetime input formats support

	$dater = new Dater\Dater(new Dater\Locale\En());
	echo $dater->datetime();
	echo $dater->datetime(time());
	echo $dater->datetime(date('Y-m-d H:i:s'));
	
### Formats binding

	$dater->format(time(), 'd/m/Y'); // 2013/03/14
	$dater->setFormat('slashedDate', 'd/m/Y');
	$dater->format(time(), 'slashedDate'); // 2013/03/14
	$dater->slashedDate(time()); // 2013/03/14

### Format options

All [date()](http://php.net/date) format options available and can be overrided or extended:

	$dater->addFormatOption('ago', function (DateTime $datetime) {
		return floor((time() - $datetime->getTimestamp()) / 86400) . ' days ago';
	});
	$dater->format(time() - 60*60*24*7, 'd F Y, ago'); // 14 March 2013, 7 days ago

### Locales support

	$dater->setLocale(new Dater\Locale\En()); // or you can use Dater\Dater::getLocaleByCode('ru')
	echo $dater->date(); // 03/21/2013
	echo $dater->now('j F Y'); // 21 March 2013
	
	$dater->setLocale(new Dater\Locale\Ru());
	echo $dater->date(); // 21.03.2013
	echo $dater->now('j F Y'); // 21 марта 2013
	
### Standard server & user format methods

	echo $dater->date(); // 03/21/2013 (client timezone, depends on locale)
	echo $dater->time(); // 5:41 AM (client timezone, depends on locale)
	echo $dater->datetime(); // 03/21/2013 5:41 (client timezone, depends on locale)
	echo $dater->isoDate(); // 2013-03-21 (client timezone)
	echo $dater->isoTime(); // 05:41:28 (client timezone)
	echo $dater->isoDatetime(); // 2013-03-21 05:41:28 (client timezone)
	echo $dater->serverDate(); // 2013-03-21 (server timezone)
	echo $dater->serverTime(); // 09:41:28 (server timezone)
	echo $dater->serverDatetime(); // 2013-03-21 09:41:28 (server timezone)

### Native PHP DateTime class object init & formatting

	$datetime = $dater->initDatetimeObject('2013-03-21 08:18:06', 'UTC', 'Europe/London');
	$datetime->modify('+10 years');
	echo $dater->formatDatetimeObject($datetime, 'date'); // 03/21/2013
	// or same thing in one line with Dater\Dater :)
	echo $dater->modify('2013-03-21 08:18:06', 'date', 'UTC', 'Europe/London'); // 03/21/2013

### Timezones conversion

	$dater->setServerTimezone('Europe/Moscow');
	$dater->setClientTimezone('Europe/London');
	echo $dater->serverDatetime(); // 2013-03-21 08:18:06
	echo $dater->isoDatetime(); // 2013-03-21 04:18:06
	echo $dater->time(); // 04:18
	
### Timezone auto-detection

Based on JavaScript [jsTimezoneDetect](http://pellepim.bitbucket.org/jstz/) library with sending result to server by COOKIE.

	$timezoneDetector = new Dater\TimezoneDetector();
	echo '<html><head>' . $timezoneDetector->getHtmlJsCode() .'</head></html>'; // <script>...</script>
	echo $timezoneDetector->getClientTimezone(); // Europe/London
	
### Convert request datetime to server timezone

Is useful to auto-convert all client request datetime data to server timezone.

	$_GET = array('filter' => array('startsFrom' => '2012-12-12 12:00:00'));
	$_POST = array('event' => array('starts' => '2012-12-12 12:00:00'));
	$_REQUEST = array_merge($_GET, $_POST);
	$daterDataHandler = new Dater\DataHandler($dater);
	$daterDataHandler->convertRequestDataToServerTimezone(); // all '2012-12-12 12:00:00' replaced to '2012-12-12 10:00:00'

### Convert text template datetime timezone

Is useful to auto-convert all datetime in template date to client timezone. For example in Email template body.
	
	$data = 'Timestamp format: 1363238564 (will not be handled)
	Timestamp format: 1363238564[Y/m/d]
	Timestamp format: 1363238564[datetime]
	Server datetime format: 2013-03-14 09:22:44[Y/m/d]
	Server datetime format: 2013-03-14 09:22:44[time]
	Server datetime format: 2013-03-14 09:22:44';
	echo $daterDataHandler->handleDataTimezone($data); 
	
Will print:
	
	Timestamp format: 1363238564 (will not be handled)
	Timestamp format: 2013/03/14
	Timestamp format: 14.03.2013 07:22
	Server datetime format: 2013/03/14
	Server datetime format: 07:22
	Server datetime format: 2013-03-14 07:22:44
	
### Convert output datetime to client timezone
	
	$daterDataHandler->enableOutputTimezoneHandler();
	echo $data; // $data from previous example will print the same as in prevous example
	

## Recommended

* Google Chrome extension [PHP Console](http://goo.gl/b10YF)
* Google Chrome extension [JavaScript Errors Notifier](http://goo.gl/kNix9)
