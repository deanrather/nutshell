<?php
namespace nutshell\helper
{
	class String
	{
		/**
		 * 
		 * @param String $className
		 * @return String the base class name
		 */
		public static function getBaseClassName($className)
		{
			return preg_replace('/^([^\\\\]+\\\\)*/', '', $className);
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
			return preg_replace('/\\\\[^\\\\]*$/', '', $className);
		}
	}
}
?>