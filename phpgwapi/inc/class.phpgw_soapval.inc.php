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

	phpgw::import_class('phpgwapi.soapval');

	/**
	* SOAPx4 value object
	* @author Edd Dumbill <edd@usefulinc.com>
	* @author Victor Zou <victor@gigaideas.com.cn>
	* @author Dietrich Ayala <dietrich@ganx4.com>
	* @copyright Copyright (C) 1999-2000 Edd Dumbill
	* @copyright Copyright (C) 2000-2001 Victor Zou
	* @copyright Copyright (C) 2001 Dietrich Ayala
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* @version $ I d : nusoap2phpgwapi,v 1.3 2006/09/24 13:01:46 Caeies Exp $
	* @internal This project began based on code from the 2 projects below,
	* @internal and still contains some original code. The licenses of both must be respected.
	* @internal XML-RPC for PHP; SOAP for PHP
	*/

	/**
	* SOAPx4 value object
	*
	* @package phpgwapi
	* @subpackage communication
	*/
class phpgwapi_phpgw_soapval extends phpgwapi_phpgwapi_soapval 
	{
	// 	function phpgwapi_phpgw_soapval($name='',$type=False,$value=-1,$namespace=False,$type_namespace=False)
		function phpgw_soapval($name='',$type=False,$value=0,$namespace=False,$type_namespace=False)
		{
			// detect type if not passed
			if(!$type)
			{
				if(is_array($value) && count($value) >= 1)
				{
					if(ereg("[a-zA-Z0-9\-]*",key($v)))
					{
						$type = 'struct';
					}
					else
					{
						$type = 'array';
					}
				}
				elseif(is_int($v))
				{
					$type = 'int';
				}
				elseif(is_float($v) || $v == 'NaN' || $v == 'INF')
				{
					$type = 'float';
				}
				else
				{
					$type = gettype($value);
				}
			}
			// php type name mangle
			if($type == 'integer')
			{
				$type = 'int';
			}

			$this->soapTypes = $GLOBALS['soapTypes'];
			$this->name = $name;
			$this->value = '';
			$this->type = $type;
			$this->type_code = 0;
			$this->type_prefix = false;
			$this->array_type = '';
			$this->debug_flag = False;
			$this->debug_str = '';
			$this->debug("Entering soapval - name: '$name' type: '$type'");

			if($namespace)
			{
				$this->namespace = $namespace;
				if(!isset($GLOBALS['namespaces'][$namespace]))
				{
					$GLOBALS['namespaces'][$namespace] = "ns".(count($GLOBALS['namespaces'])+1);
				}
				$this->prefix = $GLOBALS['namespaces'][$namespace];
			}

			// get type prefix
			if(ereg(":",$type))
			{
				$this->type = substr(strrchr($type,':'),1,strlen(strrchr($type,':')));
				$this->type_prefix = substr($type,0,strpos($type,':'));
			}
			elseif($type_namespace)
			{
				if(!isset($GLOBALS['namespaces'][$type_namespace]))
				{
					$GLOBALS['namespaces'][$type_namespace] = 'ns'.(count($GLOBALS['namespaces'])+1);
				}
				$this->type_prefix = $GLOBALS['namespaces'][$type_namespace];
			}

			// if type namespace was not explicitly passed, and we're not in a method struct:
			elseif(!$this->type_prefix && !isset($this->namespace))
			{
				// try to get type prefix from typeMap
				if(!$this->type_prefix = $this->verify_type($type))
				{
					// else default to method namespace
					$this->type_prefix = $GLOBALS['namespaces'][$GLOBALS['methodNamespace']];
				}
			}

			// if scalar
			if($this->soapTypes[$this->type] == 1)
			{
				$this->type_code = 1;
				$this->addScalar($value,$this->type,$name);
			// if array
			}
			elseif($this->soapTypes[$this->type] == 2)
			{
				$this->type_code = 2;
				$this->addArray($value);
			// if struct
			}
			elseif($this->soapTypes[$this->type] == 3)
			{
				$this->type_code = 3;
				$this->addStruct($value);
			}
			else
			{
				//if($namespace == $GLOBALS['methodNamespace']){
					$this->type_code = 3;
					$this->addStruct($value);
				//}
			}
		}

		function addScalar($value, $type, $name="")
		{
			$this->debug("adding scalar '$name' of type '$type'");
			
			// if boolean, change value to 1 or 0
			if ($type == "boolean")
			{
				if((strcasecmp($value,"true") == 0) || ($value == 1))
				{
					$value = 1;
				}
				else
				{
					$value = 0;
				}
			}

			$this->value = $value;
			return true;
		}

		function addArray($vals)
		{
			$this->debug("adding array '$this->name' with ".count($vals)." vals");
			$this->value = array();
			if(is_array($vals) && count($vals) >= 1)
			{
				@reset($vals);
				while(list($k,$v) = @each($vals))
				/* foreach($vals as $k => $v) */
				{
					$this->debug("checking value $k : $v");
					// if soapval, add..
					if(get_class($v) == 'soapval')
					{
						$this->value[] = $v;
						$this->debug($v->debug_str);
					// else make obj and serialize
					}
					else
					{
						if(is_array($v))
						{
							if(ereg("[a-zA-Z\-]*",key($v)))
							{
								$type = 'struct';
							}
							else
							{
								$type = 'array';
							}
						}
						elseif(!ereg("^[0-9]*$",$k) && in_array($k,array_keys($this->soapTypes)))
						{
							$type = $k;
						}
						elseif(is_int($v))
						{
							$type = 'int';
						}
						elseif(is_float($v) || $v == 'NaN' || $v == 'INF')
						{
							$type = 'float';
						}
						else
						{
							$type = gettype($v);
						}
						$new_val = createObject('phpgwapi.soapval','item',$type,$v);
						$this->debug($new_val->debug_str);
						$this->value[] = $new_val;
					}
				}
			}
			return true;
		}

		function addStruct($vals)
		{
			$this->debug("adding struct '$this->name' with ".count($vals).' vals');
			if(is_array($vals) && count($vals) >= 1)
			{
				@reset($vals);
				while(list($k,$v) = @each($vals))
				/* foreach($vals as $k => $v) */
				{
					// if serialize, if soapval
					if(get_class($v) == 'soapval')
					{
						$this->value[] = $v;
						$this->debug($v->debug_str);
					// else make obj and serialize
					}
					else
					{
						if(is_array($v))
						{
							@reset($v);
							while(list($a,$b) = @each($v))
							/* foreach($v as $a => $b) */
							{
								if($a == "0")
								{
									$type = 'array';
								}
								else
								{
									$type = 'struct';
								}
								break;
							}
						}
						elseif(is_array($k) && in_array($k,array_keys($this->soapTypes)))
//						elseif(is_array($k,in_array($k,array_keys($this->soapTypes))))
						{
							$this->debug("got type '$type' for value '$v' from soapTypes array!");
							$type = $k;
						}
						elseif(is_int($v))
						{
							$type = 'int';
						}
						elseif(is_float($v) || $v == "NaN" || $v == "INF")
						{
							$type = 'float';
						}
						else
						{
							$type = gettype($v);
							$this->debug("got type '$type' for value '$v' from php gettype()!");
						}
						$new_val = createObject('phpgwapi.soapval',$k,$type,$v);
						$this->debug($new_val->debug_str);
						$this->value[] = $new_val;
					}
				}
			}
			else
			{
				$this->value = array();
			}
			return true;
		}

		// turn soapvals into xml, woohoo!
		function serializeval($soapval=false)
		{
			if(!$soapval)
			{
				$soapval = $this;
			}
			$this->debug("serializing '$soapval->name' of type '$soapval->type'");
			if($soapval->name == '')
			{
				$soapval->name = 'return';
			}

			switch($soapval->type_code)
			{
				case 3:
					// struct
					$this->debug('got a struct');
					if($soapval->prefix && $soapval->type_prefix)
					{
						$xml .= "<$soapval->prefix:$soapval->name xsi:type=\"$soapval->type_prefix:$soapval->type\">\n";
					}
					elseif($soapval->type_prefix)
					{
						$xml .= "<$soapval->name xsi:type=\"$soapval->type_prefix:$soapval->type\">\n";
					}
					elseif($soapval->prefix)
					{
						$xml .= "<$soapval->prefix:$soapval->name>\n";
					}
					else
					{
						$xml .= "<$soapval->name>\n";
					}
					if(is_array($soapval->value))
					{
						@reset($soapval->value);
						while(list($k,$v) = @each($soapval->value))
						/* foreach($soapval->value as $k => $v) */
						{
							$xml .= $this->serializeval($v);
						}
					}
					if($soapval->prefix)
					{
						$xml .= "</$soapval->prefix:$soapval->name>\n";
					}
					else
					{
						$xml .= "</$soapval->name>\n";
					}
					break;
				case 2:
					// array
					@reset($soapval->value);
					while(list($null,$array_val) = @each($soapval->value))
					/* foreach($soapval->value as $array_val) */
					{
						$array_types[$array_val->type] = 1;
						$xml .= $this->serializeval($array_val);
					}
					if(count($array_types) > 1)
					{
						$array_type = 'xsd:ur-type';
					}
					elseif(count($array_types) >= 1)
					{
						$array_type = $array_val->type_prefix.":".$array_val->type;
					}

					$xml = "<$soapval->name xsi:type=\"SOAP-ENC:Array\" SOAP-ENC:arrayType=\"".$array_type."[".sizeof($soapval->value)."]\">\n".$xml."</$soapval->name>\n";
					break;
				case 1:
					$xml .= "<$soapval->name xsi:type=\"$soapval->type_prefix:$soapval->type\">$soapval->value</$soapval->name>\n";
					break;
				default:
					break;
			}
			return $xml;
		}

		function decode($soapval=false)
		{
			if(!$soapval)
			{
				$soapval = $this;
			}
			// scalar decode
			if($soapval->type_code == 1)
			{
				return $soapval->value;
			// array decode
			}
			elseif($soapval->type_code == 2)
			{
				if(is_array($soapval->value))
				{
					@reset($soapval->value);
					while(list($null,$item) = @each($soapval->value))
					/* foreach($soapval->value as $item) */
					{
						$return[] = $this->decode($item);
					}
					return $return;
				}
				else
				{
					return array();
				}
			// struct decode
			}
			elseif($soapval->type_code == 3)
			{
				if(is_array($soapval->value))
				{
					@reset($soapval->value);
					while(list($null,$item) = @each($soapval->value))
					/* foreach($soapval->value as $item) */
					{
						$return[$item->name] = $this->decode($item);
					}
					return $return;
				}
				else
				{
					return array();
				}
			}
		}

		// verify type
		function verify_type($type)
		{
			if ($type)
			{
//				global $GLOBALS['namespaces'],$GLOBALS['soapTypes'],$GLOBALS['typemap'];
//				global $GLOBALS['namespaces'],$GLOBALS['typemap'];

				@reset($GLOBALS['typemap']);
				while(list($namespace,$types) = @each($GLOBALS['typemap']))
				/* foreach($GLOBALS['typemap'] as $namespace => $types) */
				{
					if(in_array($type,$types))
					{
						return $GLOBALS['namespaces'][$namespace];
					}
				}
			}
			return false;
		}

		// alias for verify_type() - pass it a type, and it returns it's prefix
		function get_prefix($type)
		{
			if($prefix = $this->verify_type($type))
			{
				return $prefix;
			}
			return false;
		}

		function debug($string)
		{
			if($this->debug_flag)
			{
				$this->debug_str .= "$string\n";
			}
		}
	}
?>
