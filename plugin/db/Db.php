<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\db
{
	use nutshell\plugin\db\exception\DbException;

	use nutshell\Nutshell;
	use nutshell\core\plugin\Plugin;
	use nutshell\core\exception\NutshellException;
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\AbstractFactory;
	use nutshell\plugin\db\impl\AbstractDb;
	
	/**
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell-plugin
	 */
	class Db extends Plugin implements Singleton, AbstractFactory
	{
		protected static $connections = array();
		
		protected static $handlers = array();
		
		protected static $dependenciesLoaded = false;
		
		public static function loadDependencies()
		{
			if(!self::$dependenciesLoaded)
			{
				include_once(__DIR__.'/exception/DbException.php');
				include_once(__DIR__.'/impl/abstract/AbstractDb.php');
				include_once(__DIR__.'/impl/MySQL.php');
				include_once(__DIR__.'/impl/SQLite.php');
				include_once(__DIR__.'/impl/Oracle.php');
				include_once(__DIR__.'/impl/MSSQL.php');
				include_once(__DIR__.'/impl/ODBC.php');
				include_once(__DIR__.'/impl/Mongo.php');
				
				//register all the handlers
				self::$handlers[AbstractDb::HANDLER_DBLIB] 	= 'MSSQL';
				self::$handlers[AbstractDb::HANDLER_SYBASE] = 'MSSQL';
				self::$handlers[AbstractDb::HANDLER_MSSQL] 	= 'MSSQL';
				
				self::$handlers[AbstractDb::HANDLER_MYSQL] 	= 'MySQL';
				
				self::$handlers[AbstractDb::HANDLER_ORACLE] = 'Oracle';
				self::$handlers[AbstractDb::HANDLER_OCI] 	= 'Oracle';
				
				self::$handlers[AbstractDb::HANDLER_ODBC] 	= 'ODBC';
				
				self::$handlers[AbstractDb::HANDLER_SQLITE] = 'SQLite';

				self::$handlers[impl\Mongo::HANDLER_MONGO] = 'Mongo';
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
				$handlerConfig = $connectionParams->handler;
				if(!$handlerConfig) 
				{
					$handlerConfig = $pluginConfig->defaultHandler;
				}
				
				$className = null;
				
				if (isset(self::$handlers[$handlerConfig])) 
				{
					$className = sprintf('nutshell\plugin\db\impl\%s', $handlerConfig);
				} 
				else 
				{
					throw new DbException(sprintf('Unsupported DB handler: %s', $handlerConfig));
				}
				
				if(!class_exists($className, false)) 
				{
					throw new DbException(sprintf('Expected DB handler implementation could not be found: %s', $className));
				}
				
				//grabbing the connection parameters
				$options = array(
					AbstractDb::OPT_HOST 		=> $connectionParams->host,
					AbstractDb::OPT_PORT 		=> $connectionParams->port,
					AbstractDb::OPT_DB 			=> str_replace('{APP_HOME}',APP_HOME,$connectionParams->database),
					AbstractDb::OPT_USERNAME 	=> $connectionParams->username,
					AbstractDb::OPT_PASSWORD 	=> $connectionParams->password,
				);
				$connection = new $className($options);
				
				if(!$connection->isConnected())
				{
					throw new DbException(sprintf("Failed to initiate connection for %s", $connectionName));
				}
				
				if($charset = $connectionParams->charset) 
				{
					//sets the connection's charset
					$connection->setCharset($charset);
				}
				
				return $connection;
			} 
			else
			{
				throw new DbException(sprintf("Undefined connection: no configuration for connection named %s", $connectionName));
			}
		}
		
		/**
		 * Successful connection hook
		 */
		protected function onConnect() {
			//do nothing
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
		
		public function resetConnections() 
		{
			if(self::$connections) 
			{
				foreach(self::$connections as $name => $connection) 
				{
					try 
					{
						$connection->disconnect();
					}
					catch(\Exception $e) 
					{
						$this->plugin->Logger('root')->error($e->__toString());
					}
					
					unset(self::$connections[$name]);
				}
			}
		}
	}
}
?>