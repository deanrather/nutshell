<?php
namespace nutshell
{
	use nutshell\core\Component;
	use nutshell\core\config\Config;
	use \DIRECTORY_SEPARATOR;
	
	class Nutshell
	{
		public function __construct()
		{
			//Define constants.
			define('_',DIRECTORY_SEPARATOR);
			define('NS_HOME',__DIR__._);
			
			//Load the core library.
			$this->loadCoreLibrary();
		}
		
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
	}
}
?>