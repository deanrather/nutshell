<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\transfer
{
	use nutshell\behaviour\ChildFactory;
	use nutshell\behaviour\Singleton;

	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\plugin\transfer\exception\TransferException;
	
	/**
	 * @package nutshell-plugin
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * <pre>
	   Usage example:
	        // creates an FTP object
 			$oFtp = $this->plugin->Transfer()->Ftp();

			// connection settings
			$aSettings = array
			(
				'host'     => '127.0.0.1',
				'port'     => '21',
				'user'     => 'sroot',
				'password' => 'mypassword'
			);
			
			// apply settings
			$oFtp->setOptions($aSettings);

			// local file name
			$local = "/var/tmp/blablebli";
			
			// remote file name
			$remote = "blablebli";
			
			// sends the file (connect if it's not connected).
			$oFtp->put($local, $remote);
			
			// closes the connection.
			$oFtp->close();
		</pre>
	 */
	class Transfer extends Plugin implements Native, Singleton, ChildFactory
	{
		
		private static $engines = array(
			'ftp' => 'FTP',
			'ftps' => 'FTPS',
			'sftp' => 'SFTP',
			'scp' => 'SCP',
			'webdav' => 'WebDav',
			'web-dav' => 'WebDav',
		);
		
		public static function loadDependencies()
		{
			include_once __DIR__ . '/exception/TransferException.php';

			//abstract engine base
			include_once __DIR__ . '/engine/Base.php';
			
			include_once __DIR__ . '/engine/FTP.php';
			include_once __DIR__ . '/engine/FTPS.php';
			
			//abstract, required for SFTP and SCP
			include_once __DIR__ . '/engine/SSH.php';
			include_once __DIR__ . '/engine/SFTP.php';
			include_once __DIR__ . '/engine/SCP.php';
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			
		}
		
		public function __call($engine, $args)
		{
			$params = isset($args[0]) ? $args[0] : null;
			return self::runFactory($engine, $params);
		}
		
		public static function runFactory($engine, $args = null)
		{
			$engine = strtolower($engine);
			if(isset(self::$engines[$engine]))
			{
				$className = __NAMESPACE__ . '\\engine\\' . self::$engines[$engine];
				if(class_exists($className))
				{
					return new $className($args);	
				}
			}
			throw new TransferException(sprintf("Archiving engine not supported or misspelt: %s", $engine));
		}
	}
}
?>