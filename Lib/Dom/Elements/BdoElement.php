<?php
/**
  * PHPGoodies:BdoElement - BDO Element
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

require_once(realpath(dirname(__FILE__) . '/../../PHPGoodies.php'));

PHPGoodies::import('Lib.Dom.NodeElement');

/**
 * BdoElement - BDO Element
 */
class BdoElement extends NodeElement {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('bdo', 'block');
	}
}

