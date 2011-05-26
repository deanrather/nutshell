<?php
namespace nutshell
{
	use nutshell\core\Component;
	use nutshell\core\config\Config;
	use nutshell\core\Loader;
	use nutshell\core\Plugin;
	use nutshell\core\exception\Exception;
	use \DIRECTORY_SEPARATOR;
	
	class Nutshell
	{
		/**
		 * Configures all special constants and libraries linking.
		 */
		public function setup()
		{
			//Define constants.
			define('_',DIRECTORY_SEPARATOR);
			define('NS_HOME',__DIR__._);
			
			//Load the core library.
			$this->loadCoreLibrary();
		}
		
		/**
		 * Loads all the required core libraries
		 */
		private function loadCoreLibrary()
		{
			require(NS_HOME.'core'._.'Component.php');
			require(NS_HOME.'core'._.'exception'._.'Exception.php');
			require(NS_HOME.'core'._.'config'._.'Config.php');
			require(NS_HOME.'core'._.'Loader.php');
			require(NS_HOME.'core'._.'Plugin.php');
			
			Exception::register();
			Config::register();
			Loader::register();
			Plugin::register();
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
		}
	}
	
	/**
	 * 
	 * Nutshell default bootstrapper
	 */
	function bootstrap() 
	{
		Nutshell::init();
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