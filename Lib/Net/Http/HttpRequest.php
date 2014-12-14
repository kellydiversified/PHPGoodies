<?php
/**
 * PHPGoodies:HttpRequest - Objectify a pile of information about a request
 *
 * @uses HttpHeaders
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * HTTP HttpRequest
 */
class HttpRequest {

	// HTTP Request methods
	const HTTP_DELETE	= 'DELETE';
	const HTTP_GET		= 'GET';
	const HTTP_HEAD		= 'HEAD';
	const HTTP_OPTIONS	= 'OPTIONS';
	const HTTP_POST		= 'POST';
	const HTTP_PUT		= 'PUT';
	const HTTP_TRACE	= 'TRACE';

	/**
	 * A set of the above HTTP_* request methods for easy checking
	 */
	protected $requestMethods;

	/**
	 * The request information data structure
	 */
	protected $requestInfo;

	/**
	 * Constructor - does nothing but set up the empty data structure
	 *
	 * Note: pass false for the initCurrentRequest initializer if you want to make your own new
	 * HttpRequest.
	 *
	 * @param boolean $initCurrentRequest Initialize request info with that of the current
	 * client request being handled (optional, default=true)
	 */
	public function __construct($initCurrentRequest = true) {

		$this->requestMethods = self::getRequestMethods();

		$this->reset();

		if ($initCurrentRequest) {

			// If it doesn't look like we're running in an HTTP context, do nothing
			if (! isset($_SERVER['HTTP_HOST'])) return;

			// Request Method
			if (isset($_SERVER['REQUEST_METHOD'])) {
				$method = $this->setMethod($_SERVER['REQUEST_METHOD']);

				// Support for method tunnelling
				if (('POST' == $method) && isset($_SERVER['HTTP_X_HTTP_METHOD'])) {
					switch ($_SERVER['HTTP_X_HTTP_METHOD']) {
						case 'DELETE':
						case 'PUT':
							$this->setMethod($_SERVER['HTTP_X_HTTP_METHOD']);
							$this->setIsTunnelled(true);
							break;
						default:
							throw new Exception("Unexpected value for HTTP_X_HTTP_METHOD Header: '{$_SERVER['HTTP_X_HTTP_METHOD']}'");
					}
				}
				else $this->setIsTunnelled(false);
			}

			// Protocol
			$isSecure = false;
			// ref : http://stackoverflow.com/questions/1175096/how-to-find-out-if-you-are-using-https-without-serverhttps
			if (
				// Check the obvious
				(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ||
				// For load balancers/proxies that obscure this...
				(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ('https' == $_SERVER['HTTP_X_FORWARDED_PROTO'])) ||
				(isset($_SERVER['HTTP_X_FORWARDED_SSL']) && ('on' == $_SERVER['HTTP_X_FORWARDED_SSL'])) ||
				// See if any of the HTTPS-specific headers are lit up
				(isset($_SERVER['HTTPS_KEYSIZE'])) ||
				// Last chance to check the default SSL port (may be a lie, but if it is, it's a good lie!)
				(isset($_SERVER['SERVER_PORT']) && (443 == $_SERVER['SERVER_PORT']))) {
				$isSecure = true;
			}
			$this->setProtocol($_SERVER['REQUEST_SCHEME'] . ($isSecure ? 'S' : ''));

			// Hostname
			$this->setHostname($_SERVER['HTTP_HOST']);

			// Port
			$this->setPort($_SERVER['SERVER_PORT']);

			// URI
			$uri = '';
			if (isset($_SERVER['REQUEST_URI'])) {
				$parts = explode('?', $_SERVER['REQUEST_URI']);
				$uri = $parts[0];
			}
			else if (isset($_SERVER['PHP_SELF'])) {
				$uri = $_SERVER['PHP_SELF'];
			}
			else if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
			}
			$this->setUri($uri);

			// Script
			if (isset($_SERVER['SCRIPT_NAME'])) {

				if ($uri != $_SERVER['SCRIPT_NAME']) {
					// It's not something useful to set separately
					// when preparing a URL so we'll do it directly
					$this->requestInfo->script = $_SERVER['SCRIPT_NAME'];
				}
			}

			// Request data
			$this->requestInfo->data = PHPGoodies::instantiate('Lib.Data.Hash');
			if (is_array($_REQUEST) && count($_REQUEST)) {
				foreach ($_REQUEST as $name => $value) {
					$this->requestInfo->add($name, $valud);
				}
			}

			// Headers
			$this->requestInfo->headers = PHPGoodies::instantiate('Lib.Net.Http.HttpHeaders');
			$this->requestInfo->headers->receive();
		}
	}

	/**
	 * Reset the entire data structure
	 *
	 * @return object $this for chainable support...
	 */
	public function reset() {

		// preFragment=false -> protocol://hostname[:port]/uri[?queryString][#fragment]
		// preFragment=true  -> protocol://hostname[:port]/uri[#fragment][?queryString]
		$this->requestInfo = new \stdClass();
		$this->requestInfo->method = null;	// One of the defined HTTP_* request methods
		$this->requestInfo->isTunnelled = null;	// true|false to tunnel methods other than GET|POST
		$this->requestInfo->protocol = null;	// HTTP/S
		$this->requestInfo->hostname = null;	// www.yoursite.com
		$this->requestInfo->port = null;	// service port (80|443)
		$this->requestInfo->uri = null;		// entire uri up to the '?' for mapping
		$this->requestInfo->script = null;	// leading portion of uri, possibly all of it
		$this->requestInfo->queryString = null;	// everything after the '?' up to a fragment or EOL
		$this->requestInfo->data = null;	// All the name=value pair data for GET|POST, etc.
		$this->requestInfo->fragment = null;	// everything after the '#'
		$this->requestInfo->preFragment = null;	// true|false to preFragment a request being forumed
		$this->requestInfo->headers = null;	// HttpHeaders instance with the complete set

		return $this;
	}

	/**
	 * Get a complete set of supported request methods
	 *
	 * @return array filled with the string name of each supported request method
	 */
	static public function getRequestMethods() {
		return  array(
			self::HTTP_DELETE,
			self::HTTP_GET,
			self::HTTP_HEAD,
			self::HTTP_OPTIONS,
			self::HTTP_POST,
			self::HTTP_PUT,
			self::HTTP_TRACE
		);
	}

	/**
	 * Gets the request info as it stands
	 *
	 * @return object with request info properties and respective data
	 */
	public function getInfo() {
		return $this->requestInfo;
	}

	/**
	 * Gets the current data set as a formatted query string
	 *
	 * @return string URL-encoded query string with the data (may be 0-length if no data)
	 */
	public function getDataAsQueryString() {
		PHPGoodies::import('Lib.Net.Http.QueryString');
		return QueryString::getDataAsQueryString($this->requestInfo->data);
	}

	/**
	 * Setter for request protocol
	 *
	 * @param string $protocol Something like HTTP/S normally, but could be another
	 *
	 * @return object $this for chainable support...
	 */
	public function setProtocol($protocol) {
		$this->requestInfo->protocol = strtoupper($protocol);
		return $this;
	}

	/**
	 * Setter for request hostname
	 *
	 * @param string $hostname www.yoursite.com
	 *
	 * @return object $this for chainable support...
	 */
	public function setHostname($hostname) {
		$this->requestInfo->hostname = $hostname;
		return $this;
	}

	/**
	 * Setter for request port
	 *
	 * @param integer $port Probably 80 or 443 for typical environments
	 *
	 * @return object $this for chainable support...
	 */
	public function setPort($port) {
		$this->requestInfo->port = $port;
		return $this;
	}

	/**
	 * Setter for request URI
	 *
	 * @param string $uri The URI for this request
	 *
	 * @return object $this for chainable support...
	 */
	public function setUri($uri) {
		$this->requestInfo->uri = $uri;
		return $this;
	}

	/**
	 * Setter for request method
	 *
	 * @param string $method One of the supported http methods (GET/POST/OPTIONS, etc)
	 *
	 * @return object $this for chainable support...
	 */
	public function setMethod($method) {
		if (! $this->isValidMethod($method)) {
			throw new \Exception("Attempt to set an invalid request method ({$method})");
		}
		$this->requestInfo->method = strtoupper($method);
		return $this;
	}

	/**
	 * Make sure the specified protocol is a valid one
	 */
	public function isValidMethod($method) {
		return in_array(strtoupper($method), $this->requestMethods);
	}

	/**
	 * Setter for request method tunnelled state
	 *
	 * @param boolean $isTunnelled true to tunnel real method through POST as HTTP_X_HTTP_METHOD
	 *
	 * @return object $this for chainable support...
	 */
	public function setIsTunnelled($isTunnelled) {
		$this->requestInfo->isTunnelled = $isTunnelled ? true : false;
		return $this;
	}

	/**
	 * Setter for request data element(s)
	 *
	 * Note that value should really be a simple string or integer to make things easy to follow
	 * but it would technically be possibly to set an array of simple string/integer values as
	 * well. Supplying objects or any other data types here will not be rejected, but probably
	 * won't get you what you were hoping for...
	 *
	 * @param string $name Name of the data element to set
	 * @param mixed $value The value to set for this data element
	 *
	 * @return object $this for chainable support...
	 */
	public function setData($name, $value = null) {

		// If this is the first data element, initialize the array to hold data
		if (! is_array($this->requestInfo->data)) $this->requestInfo->data = array();

		// If this data element is not already in place, then we'll use a single value
		if (! isset($this->requestInfo->data[$name])) {
			$this->requestInfo->data[$name] = $value;
		}
		else {
			// Something is already in this data element - is it an array?
			if (is_array($this->requestInfo->data[$name])) {
				// Sweet - just add this value to the array
				$this->requestInfo->data[$name][] = $value;
			}
			else {
				// Convert it to an array and make the current
				// value and the new one the first two elements
				$this->requestInfo->data[$name] = array(
					$this->requestInfo->data[$name],
					$value
				);
			}
		}

		return $this;
	}

	/**
	 * Setter for request query string
	 *
	 * @param string $queryString Url-encoded query string
	 *
	 * @return object $this for chainable support...
	 */
	public function setQueryString($queryString) {
		$this->requestInfo->queryString = $queryString;
		return $this;
	}

	/**
	 * Setter for request document fragment (everything after the '#') in the URL
	 *
	 * @param string $fragment The fragment identifier
	 *
	 * @return object $this for chainable support...
	 */
	public function setFragment($fragment) {
		$this->requestInfo->fragment = $fragment;
		return $this;
	}

	/**
	 * Setter for pre-fragmenting the URL
	 *
	 * So "prefragment" is a made-up term here, but it refers to formulating an irregular URL
	 * by placing the fragment in front of the query string such as:
	 *
	 * protocol://hostname:port/uri/#fragment/?querystring
	 *
	 * This is useful for applications that perform client-side routing such as with AngularJS.
	 *
	 * @param boolean $preFragment true if we want to prefreagment the URL ahead of querystring 
	 *
	 * @return object $this for chainable support...
	 */
	public function setPreFragment($preFragment) {
		$this->requestInfo->preFragment = $preFragment ? true : false;
		return $this;
	}
}

