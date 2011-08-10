<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\session
{
	use nutshell\helper\Object;
	use nutshell\Nutshell;
	use nutshell\plugin\session\exception\SessionException;
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\AbstractFactory;
	use \stdClass;
	
	/**
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell-plugin
	 * @abstract
	 */
	abstract class Session extends Plugin implements Native, AbstractFactory
	{
		protected static $instance = null;
		
		public static function loadDependencies()
		{
			include_once(__DIR__ . '/Handler.php');
			
			include_once(__DIR__ . '/exception/SessionException.php');
			
			include_once(__DIR__ . '/behaviour/NamedSession.php');
			
			include_once(__DIR__ . '/engine/File.php');
			include_once(__DIR__ . '/engine/Db.php');
		}
		
		public static function registerBehaviours()
		{
			static::registerBehaviour
			(
				get_called_class(),
				'NamedSession',
				function($classInstance)
				{
					$session											=Nutshell::getInstance()->plugin->Session;
					$session->{Object::getBaseClassName($classInstance)}=new stdClass();
					$classInstance->session								=$session->{Object::getBaseClassName($classInstance)};
				}
			);
		}
		
		public static function runFactory($string)
		{
			if (is_null(self::$instance))
			{
				$storageEngine = self::config()->storage;
				if (is_null($storageEngine))
				{
					$storageEngine = self::SESSION_STORAGE_FILE;
				}
				
				//builds the class name for the storage engine
				$engineClass = __NAMESPACE__ . '\\engine\\' . ucfirst($storageEngine);

				if(class_exists($engineClass)) 
				{
					self::$instance = new $engineClass();
					self::$instance->init();
				}
				else
				{
		 			throw new SessionException(sprintf("Unknown session storage engine: %s", $storageEngine));
				}
			}
			return self::$instance;
		}
	}
}
