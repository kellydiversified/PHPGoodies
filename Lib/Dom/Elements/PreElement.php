<?php
/**
  * PHPGoodies:PreElement - PRE Element
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

require_once(realpath(dirname(__FILE__) . '/../../../PHPGoodies.php'));

PHPGoodies::import('Lib.Dom.NodeElement');

// Attributes
PHPGoodies::import('Lib.Dom.Attributes.ColsAttribute');
PHPGoodies::import('Lib.Dom.Attributes.WidthAttribute');
PHPGoodies::import('Lib.Dom.Attributes.WrapAttribute');

/**
 * PreElement - PRE Element
 */
class PreElement extends NodeElement {
	use ColsAttribute, WidthAttribute, WrapAttribute;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('pre', 'block');
	}
}

