<?php
/**
 * @package nutshell
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\core\config
{
	use nutshell\core\config\exception\ConfigException;
	
	/**
	 * Configuration node instance
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell
	 */
	class ConfigRoot extends Config
	{
		/**
		 * Generic method to transform a random block of data into a config object or value. 
		 * 
		 * @param Object $obj
		 * @param function $extendHandler
		 */
		public static function parseRoot($obj, $extendHandler, &$extended)
		{
			if (is_null($extended)) 
			{
				$extended = array();
			}
			
			$configRoot = parent::parse($obj);
			$config = null;
			
			if(!is_null($configRoot->extends)) 
			{
				//lookup through the extended files list
				if(in_array($configRoot->extends, $extended)) 
				{
					throw new ConfigException('Cyclic config file dependency detected!');
				}
				$parentConfig = $extendHandler($configRoot->extends, $extended);
				$config = $parentConfig->extendWithNode($configRoot->config);
			} 
			else 
			{
				$config = $configRoot->config;
			}
			
			return $config;
		}
	}
}