<?php
	/**
	* This file is generated automaticaly from the nusoap library for
	* phpGroupWare, using the nusoap2phpgwapi.php script written for this purpose by 
	* Caeies (caeies@phpgroupware.org)
	* @copyright Portions Copyright (C) 2003,2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* Please see original header after this one and class.nusoap_base.inc.php
	* @version $Id$
	*/

/* Please see class.base_nusoap.inc.php for more information */
	phpgw::import_class('phpgwapi.nusoap_base');

/**
* For creating serializable abstractions of native PHP types.  This class
* allows element name/namespace, XSD type, and XML attributes to be
* associated with a value.  This is extremely useful when WSDL is not
* used, but is also useful when WSDL is used with polymorphic types, including
* xsd:anyType and user-defined types.
*
* @author   Dietrich Ayala <dietrich@ganx4.com>
* @version  $ I d : nusoap.php,v 1.95 2006/02/02 15:52:34 snichol Exp $
* @access   public
*/
class phpgwapi_soapval extends phpgwapi_nusoap_base {
	/**
	 * The XML element name
	 *
	 * @var string
	 * @access private
	 */
	var $name;
	/**
	 * The XML type name (string or false)
	 *
	 * @var mixed
	 * @access private
	 */
	var $type;
	/**
	 * The PHP value
	 *
	 * @var mixed
	 * @access private
	 */
	var $value;
	/**
	 * The XML element namespace (string or false)
	 *
	 * @var mixed
	 * @access private
	 */
	var $element_ns;
	/**
	 * The XML type namespace (string or false)
	 *
	 * @var mixed
	 * @access private
	 */
	var $type_ns;
	/**
	 * The XML element attributes (array or false)
	 *
	 * @var mixed
	 * @access private
	 */
	var $attributes;

	/**
	* constructor
	*
	* @param    string $name optional name
	* @param    mixed $type optional type name
	* @param	mixed $value optional value
	* @param	mixed $element_ns optional namespace of value
	* @param	mixed $type_ns optional namespace of type
	* @param	mixed $attributes associative array of attributes to add to element serialization
	* @access   public
	*/
  	function phpgwapi_soapval($name='soapval',$type=false,$value=-1,$element_ns=false,$type_ns=false,$attributes=false) {
		parent::phpgwapi_nusoap_base();
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
		$this->element_ns = $element_ns;
		$this->type_ns = $type_ns;
		$this->attributes = $attributes;
    }

	/**
	* return serialized value
	*
	* @param	string $use The WSDL use value (encoded|literal)
	* @return	string XML data
	* @access   public
	*/
	function serialize($use='encoded') {
		return $this->serialize_val($this->value,$this->name,$this->type,$this->element_ns,$this->type_ns,$this->attributes,$use);
    }

	/**
	* decodes a soapval object into a PHP native type
	*
	* @return	mixed
	* @access   public
	*/
	function decode(){
		return $this->value;
	}
}



?>
