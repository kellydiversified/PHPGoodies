<?php
/**
 * PHPGoodies:NodeElement - General HTML Element Generation support
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

require_once(realpath(dirname(__FILE__) . '/../../PHPGoodies.php'));

PHPGoodies::import('Lib.Dom.Node');
PHPGoodies::import('Lib.Dom.NodeInterface');
PHPGoodies::import('Lib.Dom.NodeAttribute');

/**
 * NodeElement - General HTML Element Generation support
 */
class NodeElement extends Node implements NodeInterface {

	/**
	 * Type of the element ('inline' or 'block')
	 */
	protected $elementType;

	/**
	 * The HTML specification version we want to support (4/5)
	 */
	protected $compatability;

	/**
	 * Constructor
	 *
	 * @param string $name The kind of Html element (i.e. 'a', 'table', etc.)
	 * @param string $elementType The type of element (either 'inline' or 'block')
	 * @param integer $compatability The HTML specification version we want to support (4/5)
	 */
	public function __construct($name, $elementType, $compatability = 5) {
		parent::__construct('element');
		$this->name = $name;
		$this->elementType = $elementType;
		$this->compatability = $compatability;
	}

	/**
	 * Set the named attribute on this element to the specified value
	 *
	 * @param string $name The name of the attribute to add
	 * @param string $value The value to assign to the attribute
	 *
	 * @return object $this for chaining...
	 */
	public function setAttribute($name, $value = null) {

		// If this attribute is already set...
		$attrNode =& $this->getAttribute($name);
		if (isset($attrNode)) {

			// Update the value
			$attrNode->setValue($value);
		}
		else {

			// Make a new attribute!
			$attrNode = new NodeAttribute($name, $value);
			$this->appendNode($attrNode); 
		}
		return $this;
	}

	/**
	 * Get the named attribute if it is set
	 *
	 * @param string $name The name of the attribute we are after
	 *
	 * @return object NodeAttribute instance for the named attribute, or null if there isn't one
	 */
	public function &getAttribute($name) {
		foreach ($this->nodeList as $node) {
			if ($node->getType() != 'attribute') continue;
			if ($node->getName() == $name) return $node;
		}

		return null;
	}

	/**
	 * Remove the named attribute if it is set
	 *
	 * @param string $name The name of the attribute we are after
	 *
	 * @return object $this for chaining...
	 */
	public function removeAttribute($name) {

		// My professors urged me never to use single-letter variable names, so...
		for ($xx = 0; $xx < count($this->nodeList); $xx++) {
			if ($this->nodeList[$xx]->getType() != 'attribute') continue;
			if ($this->nodeList[$xx]->getName() == $name) {

				// Get rid of this one...
				unset($this->nodeList[$xx]);

				// And reindex the array
				$this->nodeList = array_values($this->nodeList);

				break;
			}
		}

		return $this;
	}

	/**
	 * Set the lang attribute value
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setLang($value = null) {
		$this->setAttribute('lang', $value);

		return $this;
	}

	/**
	 * Set the hidden attribute value
	 *
	 * HTML 5+ compatability only
	 *
	 * @param string $value The value to set for this attribute
	 *
	 * @return object This object for chaining...
	 */
	public function setHidden($value = null) {
		if ($this->compatability >= 5) {
			$this->setAttribute('hidden', $value);
		}
		return $this;
	}

	// TODO also add... (ref: http://www.w3schools.com/tags/ref_standardattributes.asp)
	// version 4:  accesskey, class, dir, id, lang, style, tabindex, title
	// version 5: contenteditable, contextmenu, data-*, draggable, dropzone, hidden, spellcheck, translate

	/**
	 * Turn this element node into a string
	 *
	 * @return string HTML with the rendered element
	 */
	public function toString() {

		// No matter which element type, it could have attributes
		$attributes = $this->nodeListToString(array('attribute'));

		switch ($this->elementType) {
			case 'block':
				// Block types can have other nodes/elements inside of them
				$children = $this->nodeListToString(array('element','text','comment'));
				return "<{$this->name}{$attributes}>{$children}</{$this->name}>";

			case 'inline':
				return "<{$this->name}{$attributes}/>";

			default:
				throw new Exception("Invalid element type '{$this->elementType}'");
		}
	}
}

