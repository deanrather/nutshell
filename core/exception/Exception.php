<?php
/**
 * @package nutshell
 */
namespace nutshell\core\exception
{
	use nutshell\core\Component;
	use nutshell\Nutshell;
	
	/**
	 * @package nutshell
	 */
	class Exception extends NutshellException
	{			
		public static function register() 
		{
			Component::load(array());
		}
	}	
}