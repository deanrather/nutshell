<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\db
{
	use nutshell\Nutshell;
	
	use nutshell\core\plugin\Plugin;
	
	use nutshell\core\exception\Exception;
	
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\AbstractFactory;
	
	/**
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell-plugin
	 */
	class Db extends Plugin implements Singleton, AbstractFactory
	{
		protected static $connections = array();
		
		protected static $dependenciesLoaded = false;
		
		public static function loadDependencies()
		{
			if(!self::$dependenciesLoaded)
			{
				include_once(__DIR__.'/impl/DB.php');
			}
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			
		}
		
		protected static function setupConnection($connectionName) 
		{
			$pluginConfig = Db::config();
			
			if ($connectionParams = $pluginConfig->connections->{$connectionName})
			{
				//grabbing the connection parameters
				$host = $connectionParams->host;
				$port = $connectionParams->port;
				$database = $connectionParams->database;
				$username = $connectionParams->username;
				$password = $connectionParams->password;
				
				$connection = new \DB($host, $port, $database, $username, $password);
				
				if(!$connection->isConnected())
				{
					throw new Exception(sprintf("Failed to initiate connection for %s", $connectionName));
				}
				
				return $connection;
			} 
			else
			{
				throw new Exception(sprintf("Undefined connection: no configuration for connection named %s", $connectionName));
			}
		}
		
		public static function runFactory($connectionName)
		{
			self::loadDependencies();
			if (!array_key_exists($connectionName, self::$connections))
			{
				self::$connections[$connectionName] = self::setupConnection($connectionName);
			}
			
			return self::$connections[$connectionName];
		}
	}
}
?>