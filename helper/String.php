<?php
namespace nutshell\helper
{
	class String
	{
		const CLASSINFO_NAMESPACE = 1;
		
		const CLASSINFO_CLASSNAME = 2;
		
		const CLASSINFO_VAL_NAMESPACE = 'namespace';
		
		const CLASSINFO_VAL_CLASSNAME = 'classname';
		
		
		/**
		 * 
		 * @param String $className
		 * @return String the base class name
		 */
		public static function getBaseClassName($className)
		{
			return self::getClassInfo($className, self::CLASSINFO_CLASSNAME);
		}
		
		/**
		 * 
		 * Enter description here ...
		 * 
		 * @access public
		 * @param string $className
		 * @return String
		 */
		public static function getNamespace($className)
		{
			return self::getClassInfo($className, self::CLASSINFO_NAMESPACE);
		}
		
		/**
		 * getClassInfo returns an associative array containing information about the $classname
		 * 
		 * @param String $className the class being investigated
		 * @param int $options You can specify which elements are returned with optional parameter options. It composes from String::CLASSINFO_NAMESPACE, String::CLASSINFO_CLASSNAME. It defaults to return all elements.
		 * @return array an associative array of information corresponding to the following keys: 
		 * String::CLASSINFO_VAL_NAMESPACE, String::CLASSINFO_VAL_CLASSNAME. 
		 * In case the string cannot be parsed, null is returned.
		 * 		
		 */
		public static function getClassInfo($className, $options = null)
		{
			if($options === null) {
				$options = self::CLASSINFO_NAMESPACE | self::CLASSINFO_CLASSNAME;
			}
			//init
			$buff = array(
				self::CLASSINFO_VAL_NAMESPACE => null, 
				self::CLASSINFO_VAL_CLASSNAME => null
			);
			
			//checking the pattern
			if(preg_match('/^((?:[^\\\\]+\\\\)*)([^\\\\]+)$/', $className, $matches))
			{
				$buff[self::CLASSINFO_VAL_NAMESPACE] = rtrim($matches[1], '\\');
				$buff[self::CLASSINFO_VAL_CLASSNAME] = $matches[2];
			}
			
			//composing the output
			$result = array();
			if($options & self::CLASSINFO_NAMESPACE)
			{
				$result[self::CLASSINFO_VAL_NAMESPACE] = $buff[self::CLASSINFO_VAL_NAMESPACE];
			}
			if($options & self::CLASSINFO_CLASSNAME)
			{
				$result[self::CLASSINFO_VAL_CLASSNAME] = $buff[self::CLASSINFO_VAL_CLASSNAME];
			}
			
			//if more than one result is required, return an array
			if(count($result) > 1) 
			{
				return $result;
			} 
			else
			{
				//otherwise return only the required property
				$keys = array_keys($result);
				return $result[$keys[0]];
			}
		}
	}
}
?>