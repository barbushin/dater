<?php

namespace Dater;

/**
 * Detect client timezone by JavaScript
 *
 * @see https://github.com/barbushin/dater
 * @author Sergey Barbushin
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @copyright © Sergey Barbushin, 2013. Some rights reserved.
 */
class TimezoneDetector {

	protected $cookieName;
	protected $clientTimezone;

	public function __construct($cookieName = 'dater_timezone') {
		$this->cookieName = $cookieName;
		$this->clientTimezone = $this->initClientTimezone();
	}

	protected function initClientTimezone() {
		if(isset($_COOKIE[$this->cookieName])) {
			return $_COOKIE[$this->cookieName];
		}
	}

	public function getClientTimezone() {
		return $this->clientTimezone;
	}

	/**
	 *
	 * Detection method is based on jsTimezoneDetect library http://pellepim.bitbucket.org/jstz/ + COOKIE store
	 * @param bool $reloadPageOnTimezoneChanged
	 * @param int $refreshInterval
	 * @return string|null
	 */
	public function getHtmlJsCode($reloadPageOnTimezoneChanged = true, $refreshInterval = 100) {
		return '
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js"></script>
<script type="text/javascript">
	function refreshTimezoneCookie() {
			var lastTimezone = (m = new RegExp(";?\\\\s*' . $this->cookieName . '=(.*?);", "g").exec(";" + document.cookie + ";")) ? m[1] : null;
			var currentTimezone = jstz.determine().name();
			if(!lastTimezone || (lastTimezone != currentTimezone)) {
				document.cookie = "' . $this->cookieName . '=" + jstz.determine().name() + "; path=/";' .
			($reloadPageOnTimezoneChanged ? '
				location.reload(true);' : '') . '
			}
		}
		refreshTimezoneCookie();
		setInterval(refreshTimezoneCookie, ' . $refreshInterval . ' * 1000);
</script>';
	}
}
