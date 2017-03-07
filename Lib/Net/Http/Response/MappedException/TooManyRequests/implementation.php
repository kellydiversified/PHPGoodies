<?php
/**
 * PHPGoodies:Lib_Net_Http_Response_MappedException_TooManyRequests - Extension of SPL's RuntimeException
 *
 * @uses Lib_Net_Http_Response
 * @uses Lib_Net_Http_Response_MappedException
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

PHPGoodies::import('Lib.Net.Http.Response');
PHPGoodies::import('Lib.Net.Http.Response.MappedException');

/**
 * MappedException: 429 TOO_MANY_REQUESTS
 */
class Lib_Net_Http_Response_MappedException_TooManyRequests extends Lib_Net_Http_Response_MappedException {

	/**
	 * Constructor - code is "fixed" for these mapped exceptions, so we don't allow code to be supplied
	 */
	public function __construct($message = '', $previous = null) {
		$msg = (strlen($message)) ? " - {$message}" : '';
		$code = Lib_Net_Http_Response::HTTP_TOO_MANY_REQUESTS;
		$desc = Lib_Net_Http_Response::getDescription($code);
		parent::__construct("{$desc}{$msg}", $code, $previous);
	}
}
