<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\cache
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Factory;
	use nutshell\Nutshell;
	
	/**
	 * @package nutshell-plugin
	 * @author guillaume
	 */
	class Cache extends Plugin implements Native,Singleton
	{
		/**
		 * This property stores an object that manages the cache.
		 * @var NutshellCache
		 */
		protected $oCacheManager = null;
		
		public static function loadDependencies()
		{
			include_once(__DIR__.'/impl/NutshellCache.php');
			include_once(__DIR__.'/impl/NutshellFileCache.php');
			include_once(__DIR__.'/impl/NutshellMemCache.php');
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init($type='file')
		{
			if ($type=='file')
			{
				$this->oCacheManager = new NutshellFileCache();
				$configCacheFolder = $this->nutshell->config->plugin->cache->folder;
				
				// in the case that no folder has been provided, give a default
				if strlen($configCacheFolder)==0
				{
					$configCacheFolder = APP_HOME.'cache';
				}
				$this->oCacheManager->setCacheFolder();
			}	
			else if ($type=='mem')
			{
				$this->oCacheManager = new NutshellMemCache();
			}
			else
			{
				//TODO: code error here
			}
		}
		
		/**
		 * Stores data in to the cache for the $key.
		 * @param string $cacheKey
		 * @param mixed  $data
		 * @param int    $expiryTime
		 * @param string $folder
		 * @return mixed
		 */
		public function store($cacheKey, &$data, $expiryTime, $folder='')
		{
			$this->oCacheManager->store($cacheKey, $data, $expiryTime, $folder);
		}
		
		/**
		 * Retrieves data from cache. It returns NULL when no data is found.
		 * @param string $cacheKey
		 * @return mixed
		 */
		public function retrieve($cacheKey, $subFolder='')
		{
			return $this->oCacheManager->retrieve($cacheKey, $subFolder);
		}		
	}
}
?>