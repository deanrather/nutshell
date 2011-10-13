<?php
namespace nutshell\plugin\cache
{
	class NutshellMemCache extends NutshellCache
	{
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
			//TODO: code here
		}
		
		/**
		 * Retrieves data from cache. It returns NULL when no data is found. 
		 * @param string $cacheKey
		 * @return mixed
		 */
		public function retrieve($cacheKey)
		{
			//TODO: code here
		}
	}
}