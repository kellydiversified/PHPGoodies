<?php
/**
 * PHPGoodies:TextAttribute - TEXT element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

/**
 * TextAttribute - TEXT element attribute trait for NodeElements to easily use
 */
trait TextAttribute {
	/**
	 * Set the text attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setText($value) {
		$this->setAttribute('text', $value);

		return $this;
	}

	/**
	 * Get the text attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getText() {
		return $this->getAttribute('text');
	}
}
