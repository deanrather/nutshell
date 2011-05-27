<?php
namespace nutshell
{
	use nutshell\core\Component;
	use nutshell\core\config\Config;
	use nutshell\core\Loader;
	use nutshell\core\Plugin;
	use nutshell\core\exception\Exception;
	use \DIRECTORY_SEPARATOR;
	use \DirectoryIterator;
	
	class Nutshell
	{
		const NUTSHELL_ENVIRONMENT = 'NS_ENV';
		
		public $config 	=null;
//		public $helper	=null;
		private $loader	=null;
		
		/**
		 * Configures all special constants and libraries linking.
		 */
		public function setup()
		{
			//Define constants.
			define('_DS_',DIRECTORY_SEPARATOR);
			define('NS_HOME',__DIR__._DS_);
			
			//Load the behaviours first.
			$this->loadBehaviours();
			
			//Load the core components.
			$this->loadCoreComponents();
			
			//init core components
			$this->initCoreComponents();
		}
		
		private function loadBehaviours()
		{
			foreach (new DirectoryIterator(NS_HOME.'behaviour'._DS_) as $iteration)
			{
				if (!$iteration->isDot())
				{
					require($iteration->getPathname());
				}
			}
		}
		
		/**
		 * Loads all the required core libraries
		 */
		private function loadCoreComponents()
		{
			require(NS_HOME.'core'._DS_.'Component.php');
			require(NS_HOME.'core'._DS_.'exception'._DS_.'Exception.php');
			require(NS_HOME.'core'._DS_.'config'._DS_.'Config.php');
			require(NS_HOME.'core'._DS_.'Loader.php');
			require(NS_HOME.'core'._DS_.'Plugin.php');
			
			Exception::register();
			Config::register();
			Loader::register();
			Plugin::register();
		}
		
		/**
		 * Create instances of the core components
		 */
		private function initCoreComponents() 
		{
			$this->loadCoreConfig();
			
			$this->loader=new Loader();
		}
		
		/**
		 * 
		 * Load the core config based on environment variables. Defaults to production mode.
		 */
		private function loadCoreConfig()
		{
			if (!defined(self::NUTSHELL_ENVIRONMENT))
			{
				define(self::NUTSHELL_ENVIRONMENT, 'production');
			}
			
			$this->config = Config::loadCoreConfig(NS_ENV);
		}
		
		/**
		 * Nutshell framework initialisation.
		 * Creates an instance and performs the setup.
		 * Should always be used to start the framework.
		 */
		public static function init() 
		{
			$GLOBALS['NUTSHELL'] = new Nutshell();
			$GLOBALS['NUTSHELL']->setup();
			return $GLOBALS['NUTSHELL'];
		}
		
		public static function getInstance() 
		{
			if(!$GLOBALS['NUTSHELL']) 
			{
				throw new Exception('Unexpected situation: no running Nutshell instance!');
			}
			return $GLOBALS['NUTSHELL'];
		}
		
		/*** OVERLOADING ***/
		
		public function __get($key)
		{
			if ($key=='plugin')
			{
				$loader=$this->loader;
				return $loader('plugin');
			}
			else
			{
				throw new Exception('Attempted to get invalid property "'.$key.'" from core.');
			}
		}
		
		
	}
	
	/**
	 * 
	 * Nutshell default bootstrapper
	 */
	function bootstrap() 
	{
		return Nutshell::init();
	}
}

namespace 
{
	//checks for overriding bootstrap method
	if(function_exists('bootstrap')) 
	{
		//trigger it
		booststrap();
	}
	else
	{
		//just use the default bootstrap
		nutshell\bootstrap();
	}
}
?>