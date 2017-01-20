<?php
/**
 * PHPGoodies:Lib_Dom_Attributes_Id - ID element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * Id - ID element attribute trait for NodeElements to easily use
 */
trait Lib_Dom_Attributes_Id {
	/**
	 * Set the id attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setId($value) {
		$this->setAttribute('id', $value);

		return $this;
	}

	/**
	 * Get the id attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getId() {
		return $this->getAttribute('id');
	}
}
