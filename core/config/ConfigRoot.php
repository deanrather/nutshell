<?php
namespace nutshell\core\config
{
	require_once __DIR__ . '/../Exception.php';
	require_once __DIR__ . '/Config.php';
	
	use nutshell\core\Exception;
	
	/**
	 * Configuration node instance
	 * 
	 * @author guillaume
	 *
	 */
	class ConfigRoot extends Config
	{
		/**
		 * Generic method to transform a random block of data into a config object or value. 
		 * 
		 * @param unknown_type $obj
		 * @param function $extendHandler
		 */
		public static function parse($obj, $extendHandler)
		{
			$configRoot = parent::parse($obj);
			$config = null;
			if(isset($configRoot->extends)) {
				$parentConfig = $extendHandler($configRoot->extends);
				$config = $parentConfig->extendWith($configRoot->config);
			} else {
				$config = $configRoot->config;
			}
			
			return $config;
		}
	}
}