<?php
namespace nutshell\core\exception
{
	use nutshell\core\Component;
	
	class Exception extends \Exception
	{
		public static function register() 
		{
			Component::load(array());
		}		
	}
}