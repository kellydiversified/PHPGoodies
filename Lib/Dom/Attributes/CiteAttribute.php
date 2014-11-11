<?php
/**
 * PHPGoodies:CiteAttribute - CITE element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

/**
 * CiteAttribute - CITE element attribute trait for NodeElements to easily use
 */
trait CiteAttribute {
	/**
	 * Set the cite attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setCite($value) {
		$this->setAttribute('cite', $value);

		return $this;
	}

	/**
	 * Get the cite attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getCite() {
		return $this->getAttribute('cite');
	}
}
