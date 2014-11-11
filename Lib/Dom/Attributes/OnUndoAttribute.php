<?php
/**
 * PHPGoodies:OnUndoAttribute - ONUNDO element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

/**
 * OnUndoAttribute - ONUNDO element attribute trait for NodeElements to easily use
 */
trait OnUndoAttribute {
	/**
	 * Set the onundo attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setOnUndo($value) {
		$this->setAttribute('onundo', $value);

		return $this;
	}

	/**
	 * Get the onundo attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getOnUndo() {
		return $this->getAttribute('onundo');
	}
}
