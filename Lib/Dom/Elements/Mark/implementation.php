<?php
/**
  * PHPGoodies:Lib_Dom_Elements_Mark - MARK Element
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

PHPGoodies::import('Lib.Dom.Node.Element');

/**
 * Mark - MARK Element
 */
class Lib_Dom_Elements_Mark extends Lib_Dom_Node_Element {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('mark', 'block');
	}
}
