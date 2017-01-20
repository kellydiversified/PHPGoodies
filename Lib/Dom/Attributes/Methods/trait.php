<?php
/**
 * PHPGoodies:Lib_Dom_Attributes_Methods - METHODS element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * Methods - METHODS element attribute trait for NodeElements to easily use
 */
trait Lib_Dom_Attributes_Methods {
	/**
	 * Set the methods attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setMethods($value) {
		$this->setAttribute('methods', $value);

		return $this;
	}

	/**
	 * Get the methods attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getMethods() {
		return $this->getAttribute('methods');
	}
}
