<?php
/**
 * @package nutshell
 */
namespace nutshell\core\exception
{
	use nutshell\core\Component;
	use \Exception as SPLException;
	
	/**
	 * @package nutshell
	 */
	class Exception extends SPLException
	{
		public static function register() 
		{
			Component::load(array());
		}		
	}
}