<?php
namespace nutshell\plugin\archiver
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\AbstractFactory;
	
	class Archiver extends Plugin implements Native,AbstractFactory
	{
		public static function loadDependencies()
		{
			include_once __DIR__ . '/engine/Base.php';
			include_once __DIR__ . '/engine/Zip.php';
			include_once __DIR__ . '/engine/Rar.php';
		}
		
		public static function runFactory($engine)
		{
			$className = __NAMESPACE__ . '\\engine\\' . ucfirst($engine);
			
			if (class_exists($className))
			{
				return new $className();
			}
			else
			{
				throw new ArchiverException(sprintf("Archiving engine not supported or misspelt: %s", $engine));
			}
		}
	}
}
?>