<?php
namespace nutshell\core\config
{
	use nutshell\core\Component;
	
	/**
	 * Configuration node instance
	 * 
	 * @author guillaume
	 *
	 */
	class Loader extends Complement
	{
		/**
		 * 
		 */
		public static function register()
		{
			static::load(array(
			));
		}
	}
}