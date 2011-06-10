<?php
namespace nutshell\plugin\db
{
	use nutshell\Nutshell;
	
	use nutshell\core\plugin\Plugin;
	
	use nutshell\core\exception\Exception;
	
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\AbstractFactory;
	
	class Db extends Plugin implements Singleton, AbstractFactory
	{
		protected static $connections = array(); 
		
		public static function loadDependencies()
		{
			include_once(__DIR__.'/impl/DB.php');
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
				
				return new \DB($host, $port, $database, $username, $password);
			} 
			else
			{
				throw new Exception(sprintf("Undefined connection: no configuration for connection named %s", $connectionName));
			}
		}
		
		public static function runFactory($connectionName)
		{
			if (!array_key_exists($connectionName, self::$connections))
			{
				self::$connections[$connectionName] = self::setupConnection($connectionName);
			}
			
			return self::$connections[$connectionName];
		}
	}
}
?>