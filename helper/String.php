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
		
		/**
		 * Gernates a random string with or without numeric characters.
		 * 
		 * Note that the first character is never numeric.
		 * 
		 * @access public
		 * @param Int $length - The length of the randomly generated string to generate.
		 * @param Boolean $numbers - Weather or not to include numbers.
		 * @return String - Returns a randomly generated string.
		 */
		public static function getRandomString($length=10,$numbers=true)
		{
			//Force length to be an integer.
			$length=(int)$length;
			//Generate characters in an array A-Z.
			$chars=range('a','z');
			//Include numbers?
			if ($numbers)
			{
				//Merge numbers into the character array.
				$chars=array_merge($chars,range(0-9));
			}
			//Cache the length.
			$charLength=count($chars)-1;
			//Create an empty string to return.
			$string='';
			//Set the first character flag to true.
			$first=true;
			//Loop until length is 0.
			while ($length)
			{
				//Make sure the first character is never a number.
				if ($first)
				{
					$char=0;
					while (is_numeric($char))
					{
						$char=$chars[mt_rand(0,$charLength)];
					}
					$first=false;
				}
				else
				{
					$char=$chars[mt_rand(0,$charLength)];
				}
				//Add $char to the string.
				$string.=$char;
				//Remove 1 from $length - This will cause the loop to stop if length reaches 0.
				$length--;
			}
			//Return the randomly generated string.
			return $string;
		}
	}
}
?>