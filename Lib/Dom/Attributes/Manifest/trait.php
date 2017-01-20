<?php
/**
 * PHPGoodies:Lib_Dom_Attributes_Manifest - MANIFEST element attribute trait for NodeElements to easily use
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * Manifest - MANIFEST element attribute trait for NodeElements to easily use
 */
trait Lib_Dom_Attributes_Manifest {
	/**
	 * Set the manifest attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setManifest($value) {
		$this->setAttribute('manifest', $value);

		return $this;
	}

	/**
	 * Get the manifest attribute's current value
	 *
	 * @return string The attribute's current value or null if not set
	 */
	public function getManifest() {
		return $this->getAttribute('manifest');
	}
}
