<?php
/**
  * PHPGoodies:Lib_Dom_Elements_Bdo - BDO Element
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

PHPGoodies::import('Lib.Dom.Node.Element');

/**
 * Bdo - BDO Element
 */
class Lib_Dom_Elements_Bdo extends Lib_Dom_Node_Element {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('bdo', 'block');
	}
}
