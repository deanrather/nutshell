<?php
/**
 * @package nutshell-plugin
 * @author Timothy Chandler <tim.chandler@spinifexgroup.com>
 */
namespace nutshell\plugin\application
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\AbstractFactory;
	
	/**
	 * 
	 * @package nutshell-plugin
	 * @author Timothy Chandler <tim.chandler@spinifexgroup.com>
	 */
	class Application extends Plugin implements Native,AbstractFactory
	{
		public static function loadDependencies()
		{
			
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public static function runFactory($engine)
		{
// 			$className = __NAMESPACE__ . '\\engine\\' . ucfirst($engine);
			
// 			if (class_exists($className))
// 			{
// 				return new $className();
// 			}
// 			else
// 			{
// 				throw new ArchiverException(sprintf("Archiving engine not supported or misspelt: %s", $engine));
// 			}
		}
		
		/**
		* This method runs a "require_once" for each file in the folder.
		* @param string $folder
		*/
		public function loadAllFilesFromFolder($folder)
		{
			if (is_dir($folder))
			{
				foreach (new DirectoryIterator($folder) as $iteration)
				{
					if (!$iteration->isDot())
					{
						$file_name = $iteration->getPathname();
						require_once($file_name);
					}
				}
			}
		}
		
		/**
		* This method loads all application helpers.
		*/
		private function loadApplicationHelpers()
		{
			$applicationHelperFolder = __DIR__.DIRECTORY_SEPARATOR.'helper'.DIRECTORY_SEPARATOR;
			$this->loadAllFilesFromFolder($applicationHelperFolder);
		}
	}
}
?>