<?php
/**
  * PHPGoodies:Lib_Dom_Elements_THead - THEAD Element
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

PHPGoodies::import('Lib.Dom.Node.Element');

// Attributes
PHPGoodies::import('Lib.Dom.Attributes.AlignAttribute');
PHPGoodies::import('Lib.Dom.Attributes.BgColorAttribute');
PHPGoodies::import('Lib.Dom.Attributes.CharAttribute');
PHPGoodies::import('Lib.Dom.Attributes.CharOffAttribute');
PHPGoodies::import('Lib.Dom.Attributes.SpanAttribute');
PHPGoodies::import('Lib.Dom.Attributes.VAlignAttribute');

/**
 * THead - THEAD Element
 */
class Lib_Dom_Elements_THead extends Lib_Dom_Node_Element {
	use AlignAttribute, BgTHeadorAttribute, CharAttribute, CharOffAttribute, SpanAttribute;
	use VAlignAttribute, WidthAttribute;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct('thead', 'block');
	}
}
