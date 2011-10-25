<?php
namespace nutshell\plugin\cache
{
	class NutshellFileCache extends NutshellCache
	{
		/**
		 * File system cache folder
		 * @var string
		 */
		protected $cacheFolder = '';
		
		/**
		 * Defines the global file system cache folder.
		 * @param unknown_type $folder
		 */
		public function setCacheFolder($folder)
		{
			$this->cacheFolder = $folder;
		}

		/**
		 * Stores data in to the cache for the $key. 
		 * @param string $cacheKey
		 * @param mixed  $data
		 * @param int    $expiryTime
		 * @param string $subFolder
		 * @return boolean Returns true when storing works correctly.
		 */
		public function store($cacheKey, &$data, $expiryTime, $subFolder='')
		{
			$keyMD5 = md5($cacheKey);
			
			if (strlen($subFolder)>0)
			{
				$subFolder = $subFolder._DS_;
			}
			
			$fileName    = $this->cacheFolder.$subFolder.'cache_'.$keyMD5;
			$tmpFileName = $this->cacheFolder.$subFolder.'tmp_'.$keyMD5;
			
			$cacheLiteral = serialize
			(
				array
				(
					'key'    => $cacheKey,
					'expiry' => $expiryTime + time(),
					'data'   => $data
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
		public function retrieve($cacheKey, $subFolder='')
		{
			$keyMD5 = md5($cacheKey);
			$now = time();
			
			if (strlen($subFolder)>0)
			{
				$subFolder = $subFolder._DS_;
			}
			
			$fileName = $this->cacheFolder._DS_.$subFolder.'cache_'.$keyMD5;
			
			try 
			{	
				if (file_exists($fileName))
				{	
					
					if ($handle = fopen($fileName, "rb"))
					{
						$unserializedData = fread($handle, filesize($fileName));
						$data = unserialize($unserializedData);
						
						// is it the same key?
						if ($data['key'] == $cacheKey)
						{
							// is the cached value still valid?
							if ($now < $data['expiry'])
							{
								return $data['data'];
							}
						}
					}
				}
			} catch (Exception $e) 
			{
				// nothing can be done when a cache load fails.
				// no exception should be provoked
			}

			return null;
		}
	}
}