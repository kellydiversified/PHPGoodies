<?php
/**
 * PHPGoodies:Lib_Api_Rest_JsonApi_Server_Errors - JSON:API Error Collection for response documents
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

PHPGoodies::import('Lib.Data.Collection');

/**
 * JSON:API Error Collection for response documents
 */
class Lib_Net_Api_Rest_JsonApi_Server_Errors extends Lib_Data_Collection implements \JsonSerializable {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('Lib_Net_Api_Rest_JsonApi_Server_Error');
	}
	
	/**
	 * JsonSerializable Json Serializer
	 *
	 * @return Associative array of properties which will be encoded as the JSON representation
	 * of object instances of this class
	 */
	public function jsonSerialize() {
		$errors = array();
		$this->iterate(
			function ($error) use (&$errors) {
				$errors[] = $error;
			}
		);
		return $errors;
	}
}
