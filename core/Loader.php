<?php
namespace nutshell\core
{
	use nutshell\core\Component;
	
	/**
	 * Configuration node instance
	 * 
	 * @author guillaume
	 *
	 */
	class Loader extends Component
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