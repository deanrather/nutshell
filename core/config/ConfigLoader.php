<?php
namespace nutshell\core\config
{
	require_once __DIR__ . '/../Exception.php';
	require_once __DIR__ . '/ConfigRoot.php';
	
	use nutshell\core\Exception;
	
	/**
	 * 
	 * Enter description here ...
	 * @author guillaume
	 *
	 */
	class ConfigLoader
	{
		/**
		 * Name of the default base config file to open if no environment is specified.
		 * 
		 * @var String
		 */		
		const DEFAULT_ENVIRONMENT = 'default';
		
		/**
		 * Label of the config folder on disk
		 * 
		 * @var String
		 */
		const CONFIG_FOLDER = 'config';
		
		/**
		 * Config file extension
		 * 
		 * @var String
		 */
		const CONFIG_FILE_EXTENSION = 'json';

		/**
		 * Cache for the core config path
		 * 
		 * @var String
		 */
		protected static $default_core_config_path = null;
		
		/**
		 * 
		 * 
		 * @param String $environment
		 */
		public static function loadCoreConfig($environment = null) 
		{
			if(!$environment) 
			{
				$environment = self::DEFAULT_ENVIRONMENT;
			}	
			
			//computing the file path of the require environment
			$configFile = self::getCoreConfigPath() . \DIRECTORY_SEPARATOR . sprintf('%s.json', $environment);
			
			return self::loadConfigFile($configFile);
		}
		
		/**
		 * Loads 
		 * Enter description here ...
		 */
		public static function loadPluginConfig($pluginName, $environment = null) 
		{
			if(!$environment) 
			{
				$environment = self::DEFAULT_ENVIRONMENT;
			}	
			
			$configFile = '';
			
			return self::loadConfigFile($configFile);
		}
		
		/**
		 * Loads the specified file into a config instance
		 * 
		 * @param string $file
		 * @throws Exception if the file could not be found/read
		 * @return nutshell\config\Config an instance of the config
		 */
		public static function loadConfigFile($file, $extendHandler = null, &$extended = null) 
		{
			if($extendHandler === null) 
			{
				$extendHandler = function($environment, &$extended) use ($file)
				{
					return ConfigLoader::loadConfigFile(
						dirname($file) 
						. \DIRECTORY_SEPARATOR 
						. $environment 
						. '.' . ConfigLoader::CONFIG_FILE_EXTENSION,
						null,
						$extended
					);
				};
			}
			if(is_file($file)) 
			{
				if(is_readable($file)) 
				{
					if ($extended === null) 
					{
						$extended = array();
					}
					$extended[] = basename($file, '.' . self::CONFIG_FILE_EXTENSION);
					return ConfigRoot::parse(json_decode(file_get_contents($file)), $extendHandler, $extended);
				} 
				else
				{
					throw new Exception(sprintf('File is not accessible for reading: %s', $file));
				}  
			} 
			else 
			{
				throw new Exception(sprintf('Could not find file: %s', $file));
			}
		}
		
		/**
		 * Returns the core config path
		 * 
		 */
		protected static function getCoreConfigPath() 
		{
			if (self::$default_core_config_path === null) 
			{
				self::$default_core_config_path = realpath(
					//calculate the nesting level and repeat
					__DIR__ 
					. \DIRECTORY_SEPARATOR  
					. str_repeat('..' . \DIRECTORY_SEPARATOR, substr_count(__NAMESPACE__, '\\'))
					. self::CONFIG_FOLDER
				);
				
				if (self::$default_core_config_path === null) 
				{
					//the path could not be resolved => it doesn't exist
					throw new Exception('Could not find the config');
				}
			}
			
			return self::$default_core_config_path;
		}
	}
}