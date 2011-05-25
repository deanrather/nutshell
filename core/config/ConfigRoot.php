<?php
namespace nutshell\core\config
{
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
		 * @param Object $obj
		 * @param function $extendHandler
		 */
		public static function parse($obj, $extendHandler, &$extended)
		{
			if ($extended === null) 
			{
				$extended = array();
			}
			
			$configRoot = parent::parse($obj);
			$config = null;
			
			if($configRoot->extends !== null) 
			{
				//lookup through the extended files list
				if(in_array($configRoot->extends, $extended)) 
				{
					throw new Exception('Cyclic config file dependency detected!');
				}
				$parentConfig = $extendHandler($configRoot->extends, $extended);
				$config = $parentConfig->extendWith($configRoot->config);
			} 
			else 
			{
				$config = $configRoot->config;
			}
			
			return $config;
		}
	}
}