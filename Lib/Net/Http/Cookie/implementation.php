<?php
/**
 * PHPGoodies:Lib_Net_Http_Cookie - An class for working with an HTTP Cookie
 *
 * ref: http://www.nczonline.net/blog/2009/05/05/http-cookies-explained/
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * HttpCookie - An class for working with an HTTP Cookie
 */
class Lib_Net_Http_Cookie {

	/**
	 * Extracted automatically when value looks like a name/value pair
	 */
	protected $name;

	/**
	 * Plain text value for the cookie, possibly name=value pair
	 */
	protected $value;

	/**
	 * Expiration date/time stored as a timestamp so we can do useful work with it
	 */
	protected $expires;

	/**
	 * The domain for this cookie
	 */
	protected $domain;

	/**
	 * The path restriction for this cookie relative to the domain
	 */
	protected $path;

	/**
	 * Boolean indication of whether this cookie may be sent over SSL connections only
	 */
	protected $secure;

	/**
	 * Boolean indication of whether this cookie should be accessible to the server only (no-JS)
	 */
	protected $httpOnly;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->setDefaults();
	}

	/**
	 * Set default values for all our properties
	 */
	protected function setDefaults() {
		$this->setValue('');
		$this->setExpires(null);
		$this->setDomain(null);
		$this->setPath(null);
		$this->setSecure(false);
		$this->setHttpOnly(false);
	}

	/**
	 * Getter for our name property
	 *
	 * Note that there is no matching setter; we extract name from the value when the value is
	 * set to ensure that it doesn't live in both properties.
	 *
	 * @return string name for this cookie if set, else null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Setter for our value property
	 *
	 * @param string $value The value string we want to set this cookie to
	 *
	 * @return object $this for chaining...
	 */
	public function setValue($value) {

		// Does it look like a name/value pair?
		$pos = strpos($value, '=');
		if ($pos === false) {
			$this->value = $value;
			$this->name = null;
		}
		else {
			// Name, first '=', then value
			$this->name = substr($value, 0, $pos);
			$this->value = substr($value, $pos + 1);
		}
		return $this;
	}

	/**
	 * Getter for our value property
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Setter for our value property
	 *
	 * @param integer $timestamp A regular UNIX timestamp
	 *
	 * @return object $this for chaining...
	 */
	public function setExpires($timestamp) {
		$this->expires = $timestamp;
		return $this;
	}

	/**
	 * Getter for our value property
	 */
	public function getExpires() {
		return $this->expires;
	}

	/**
	 * Setter for our value property
	 *
	 * @param string $domain The domain resstriction for this cookie
	 *
	 * @return object $this for chaining...
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}

	/**
	 * Getter for our value property
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * Setter for our value property
	 *
	 * @param string $path The path restriction for this cokie
	 *
	 * @return object $this for chaining...
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/**
	 * Getter for our value property
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Setter for our value property
	 *
	 * @param boolean Set to true to require that this cookie only be sent over SSL connections
	 *
	 * @return object $this for chaining...
	 */
	public function setSecure($enable) {
		$this->secure = $enable ? true : false;
		return $this;
	}

	/**
	 * Getter for our value property
	 */
	public function getSecure() {
		return $this->secure;
	}

	/**
	 * Setter for our value property
	 *
	 * @param boolean Set to true to prevent JavaScript/client from seeing this cookie
	 *
	 * @return object $this for chaining...
	 */
	public function setHttpOnly($enable) {
		$this->httpOnly = $enable ? true : false;
		return $this;
	}

	/**
	 * Getter for our value property
	 */
	public function getHttpOnly() {
		return $this->httpOnly;
	}

	/**
	 * Extract our properties from the cookie header supplied
	 *
	 * @param string $cookie The cookie received from a header
	 */
	public function extractFromHeader($cookie) {
		$this->setDefaults();
		$crumbs = explode(';', $cookie);
		for ($i = 0; $i < count($crumbs); $i++) {
			$crumb = trim($crumbs[$i]);

			// The value is always the first one
			if (0 == $i) {
				$this->value = $this->decode($crumb);
				continue;
			}			

			// Everything else should be name=value format...
			$bits = explode('=', $crumb);
			switch (strtolower($bits[0])) {
				case 'expires':
					// Parse date time from "Sat, 03 May 2025 17:44:22 GMT"
					// "D, d F Y H:i:s T"
					$this->expires = strtotime(trim($bits[1]));
					break;

				case 'domain':
					$this->domain = trim($bits[1]);
					break;

				case 'path':
					$this->path = trim($bits[1]);
					break;

				case 'secure':
					$this->secure = true;
					break;

				case 'httponly':
					$this->httpOnly = true;
					break;
			}
		}
	}

	/**
	 * Pack our name/value into a "cookie" header that a client can send to a server
	 *
	 * @return string The "Cookie" header string (just the value portion of the header...)
	 */
	public function formatForRequestHeader() {
		$encoded = is_null($this->name) ? '' : $this->encode($this->name) . '=';
		$encoded .= $this->encode($this->value);
		return $encoded;
	}

	/**
	 * Pack our properties into a "Set-Cookie" header that a server can send to a client
	 *
	 * @return string The "Set-Cookie" header string (just the value portion of the header...)
	 */
	public function formatForResponseHeader() {

		// Response header's name/value is formatted the same as the request header is
		$encoded = $this->formatForRequestHeader();

		// Get all the other cookie properties together
		$formatted = '';
		if (! is_null($this->expires)) {
			$expiration = date('D, d F Y H:i:s T', $this->expires);
			$formatted .= "; expires={$expiration}";
		}
		if (! is_null($this->domain)) $formatted .= "; domain={$this->domain}";
		if (! is_null($this->path)) $formatted .= "; path={$this->path}";
		if ($this->secure) $formatted .= '; secure';
		if ($this->httpOnly) $formatted .= '; HttpOnly';

		// 4KB size enforcement on the entire cookie:
		$maxsize = 4196 - strlen($formatted);
		if (strlen($encoded) > $maxsize) {

			// Truncate
			$encoded = substr($encoded, 0, $maxsize);
		}

		return $encoded . $formatted;
	}

	/**
	 * Encode the supplied value to make it "cookie-safe"
	 *
	 * @param string $value The value we expect to save as a cookie
	 *
	 * @return string A cookie-safe version of the value with all the special chars encoded
	 */
	protected function encode($value) {
		$encoded = '';
		for ($xx = 0; $xx < strlen($value); $xx++) {
			$chr = $value{$xx};
			$ord = ord($chr);

			// Encode semicolon, comma, white-space (<=32), and any character > 127
			if (($ord <= 32) || ($ord >= 128) || ($ord == 59) || ($ord == 44)) {
				$encoded .= urlencode($chr);
			}
			else $encoded .= $chr;
		}

		return $encoded;
	}

	/**
	 * Decode the supplied value to get the original value back out
	 *
	 * @param string $value The previously encoded value that we want to decode
	 *
	 * @return string The original value that we encoded
	 */
	protected function decode($value) {
		return urldecode($value);
	}
}
