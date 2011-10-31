<?php
namespace nutshell\plugin\format
{
	use nutshell\behaviour\AbstractFactory;
	use nutshell\core\plugin\Plugin;
	use nutshell\core\exception\Exception;

	/**
	 * This plugins has been ported from the "format" model of leadgen 1.
	 * This plugin is a factory of formating classes. Please have a look at getAvailableFormats for a complete list.
	 */
	class Format extends Plugin implements AbstractFactory
	{
	
		/**
		 * This function returns all available formats.
		 */
		public static function getAvailableFormats() {
			return array
			(
				// egine name (or format name) => class name
				'csv' => 'Csv',
				'tsv' => 'Tsv',
				'psv' => 'Psv',
				'xml' => 'Xml',
				'json'=> 'Json',
				'adf' => 'adf'
			);
		}
		
		public static function loadDependencies()
		{
			include_once __DIR__ . _DS_ .'FormatBase.php';
			include_once __DIR__ . _DS_ .'Dsv.php';
			include_once __DIR__ . _DS_ .'Csv.php';
			include_once __DIR__ . _DS_ .'Psv.php';
			include_once __DIR__ . _DS_ .'Json.php';
			include_once __DIR__ . _DS_ .'Tsv.php';
			include_once __DIR__ . _DS_ .'Xml.php';
			include_once __DIR__ . _DS_ .'Adf.php';
		}
			
		public static function registerBehaviours()
		{
				
		}
	
		public function init()
		{
				
		}
	
		/*
		public function __call($engine, $args)
		{
			$params = isset($args[0]) ? $args[0] : null;
			return self::runFactory($engine, $params);
		}
		*/
	
		public static function runFactory($engine, $args = null)
		{
			self::loadDependencies();
			$engine = strtolower($engine);
			$engines = self::getAvailableFormats();
			if(isset($engines[$engine]))
			{
				$className = __NAMESPACE__ . '\\' . $engines[$engine];
				return new $className($args);
			}
			else
			{
				throw new Exception("Engine $engine isn't supported.");
			}
		}
	}	
}
