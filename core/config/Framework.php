<?php
namespace nutshell\core\config 
{
	use nutshell\core\exception\Exception;

	use nutshell\Nutshell;

	class Framework
	{
		const CONFIG_CACHE_FOLDER = 'cache';
		
		const CONFIG_CACHE_FILE = 'cache.json';
		
		/**
		 * 15 mins
		 * 
		 * 15 * 60
		 */
		const CONFIG_EXPIRATION = 900;
		
		const CONFIG_REBUILD_KEY = '__config';
		
		public static function loadConfig($configPath, $environment) 
		{
			$config = self::hasToRebuild() ? 
				self::rebuild($configPath, $environment) : 
				self::loadCachedConfig();
			
			return $config;
		}
		
		protected static function hasToRebuild()
		{
			$rebuildCache = false;
			
			$folder = self::getCachedConfigFolder();
			
			if(!file_exists($folder))
			{
				if(!mkdir($folder))
				{
					throw new Exception(sprintf("Could not create the config cache folder. Please check the write permission on %.", dirname($folder)));
				}
			}
			else if(!is_dir($folder))
			{
				throw new Exception(sprintf("%s does not resolve to a directory.", $folder));
			}
			else if(!is_executable($folder) || !is_writeable($folder))
			{
				throw new Exception(sprintf("Nutshell requires write and execute permission on %s", $folder));
			}
			
			
			$file = self::getCachedConfigFile();
			
			if(!file_exists($file))
			{
				if(!is_writeable($folder))
				{
					throw new Exception(sprintf("%s is not a writeable directory.", $folder));
				}
				$rebuildCache = true;
			}
			else if(!is_file($file)) 
			{
				throw new Exception(sprintf("%s does not resolve to a regular file.", $file));
			}
			else if(!is_readable($file) || !is_writeable($file)) 
			{
				throw new Exception(sprintf("Nutshell requires read and write permissions on %s.", $file));
			}
			else if(self::forceRebuild() || time() - filemtime($file) > self::CONFIG_EXPIRATION)
			{
				$rebuildCache = true;
			}
			
			return $rebuildCache;
		}
		
		protected static function forceRebuild()
		{
			return 
			
				//DEBUG
				//(NS_INTERFACE == Nutshell::INTERFACE_CLI)
				false 
				|| (NS_INTERFACE == Nutshell::INTERFACE_HTTP && $_GET[self::CONFIG_REBUILD_KEY])
			;
		}
		
		protected static function rebuild($configPath, $environment)
		{
			$config = new Config();
			$config->config = Config::loadCoreConfig($configPath, $environment);
			
			if(file_put_contents(self::getCachedConfigFile(), $config->__toString()) === false)
			{
				throw new Exception(sprintf("Failed to write to file %s.", self::getCachedConfigFile()));
			}
			return $config;
		}
		
		protected static function loadCachedConfig()
		{
			$file = self::getCachedConfigFile();
			$config = Config::loadConfigFile(dirname($file), basename($file));
			return $config;
		}
		
		protected static function getCachedConfigFolder() 
		{
			return Nutshell::getConfigPath() . _DS_ . self::CONFIG_CACHE_FOLDER;
		}
		
		protected static function getCachedConfigFile() 
		{
			return self::getCachedConfigFolder() . _DS_ . self::CONFIG_CACHE_FILE;
		}
	}
}