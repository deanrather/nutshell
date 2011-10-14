<?php
namespace nutshell\plugin\cache
{
	abstract class NutshellCache
	{
		/**
		 * Stores data in to the cache for the $key. 
		 * @param string $cacheKey
		 * @param mixed  $data
		 * @param int    $expiryTime
		 * @param string $subFolder
		 * @return mixed
		 */
		abstract public function store($cacheKey, &$data, $expiryTime, $subFolder='');
		
		/**
		 * Retrieves data from cache. It returns NULL when no data is found. 
		 * @param string $cacheKey
		 * @param string $subFolder
		 * @return mixed
		 */
		abstract public function retrieve($cacheKey, $subFolder='');
	}
}