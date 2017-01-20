<?php
/**
 * PHPGoodies:Lib_Dom_Attributes_Method - METHOD element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * Method - METHOD element attribute trait for NodeElements to easily use
 */
trait Lib_Dom_Attributes_Method {
	/**
	 * Set the method attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setMethod($value) {
		$this->setAttribute('method', $value);

		return $this;
	}

	/**
	 * Get the method attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getMethod() {
		return $this->getAttribute('method');
	}
}
