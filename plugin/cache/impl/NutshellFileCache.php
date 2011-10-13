<?php
namespace nutshell\plugin\cache
{
	class NutshellFileCache extends NutshellCache
	{
		/**
		 * Stores data in to the cache for the $key. 
		 * @param string $cacheKey
		 * @param mixed  $data
		 * @param int    $expiryTime
		 * @param string $folder
		 * @return boolean Returns true when storing works correctly.
		 */
		public function store($cacheKey, &$data, $expiryTime, $folder='')
		{
			//TODO: code here
			$cacheFolder   = '';
			
			$keyMD5 = md5($cacheKey);
			
			if (strlen($folder)>0)
			{
				$folder = $folder._DS_;
			}
			
			$fileName    = $cacheFolder._DS_.$folder.'cache_'.$keyMD5;
			$tmpFileName = $cacheFolder._DS_.$folder.'tmp_'.$keyMD5;
			
			$cacheLiteral = serialize
			(
				array
				(
					'key'    = $cacheKey,
					'expiry' = $expiryTime,
					'data'   = $data
				)
			);
			
			// Writes to a temp file and then renames to keep atomicity.
			if ($fp = fopen($tmpFileName, 'wb')) 
			{
				fwrite ($fp, $cacheLiteral, strlen($cacheLiteral));
				fclose ($fp);
			
				if (!rename($tmpFileName, $fileName)) 
				{
					// On some systems rename() doesn't overwrite destination
					unlink($filename);
					if (!rename($tmpFileName, $fileName)) 
					{
						// Make sure that no temporary file is left over
						// if the destination is not writable
						unlink($tmpFileName);
					}
				}
				chmod($fileName, 0777);
				return true;
			}
			return false;
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