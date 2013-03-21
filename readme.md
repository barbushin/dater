# About

This library can be very helpful to standartize date-time formats in your project & get done easy with different clients timezones.

# Features

* Formats binding: "time" => "H:i", "date" => "d/m/Y"
* Formats options extending
	$dater->addFormatOption('ago', function (DateTime $dateTime) {
		return floor((time() - $dateTime->getTimestamp()) / 86400) . ' days ago';
	});

See [/example/index.php](https://github.com/barbushin/dater/blob/master/example/index.php) for all features using example.

## Recommended
* Google Chrome extension [PHP Console](http://goo.gl/b10YF)
* Google Chrome extension [JavaScript Errors Notifier](http://goo.gl/kNix9)
