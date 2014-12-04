<?php
/**
  * PHPGoodies:HeadElement - HEAD Element
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

PHPGoodies::import('Lib.Dom.NodeElement');

// Attributes
PHPGoodies::import('Lib.Dom.Attributes.ProfileAttribute');


/**
 * HeadElement - HEAD Element
 */
class HeadElement extends NodeElement {
	use ProfileAttribute;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('head', 'block');
	}
}

