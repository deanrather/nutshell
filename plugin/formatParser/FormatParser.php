<?php
namespace nutshell\plugin\formatParser
{
	use nutshell\behaviour\AbstractFactory;
	use nutshell\core\plugin\Plugin;
	use nutshell\core\exception\NutshellException;

	use nutshell\Nutshell;

	class FormatParser extends Plugin implements AbstractFactory
	{
	
		/**
		 * This function returns all available formats.
		 */
		public static function getAvailableFormats() {
			return array
			(
				// engine name (or format name) => class name
				'csv' 			=> 'Csv',
				'tsv' 			=> 'Tsv',
				'psv' 			=> 'Psv',
				'json'			=> 'Json',
				'dsv' 			=> 'Dsv',
			);
		}
		
		public static function loadDependencies()
		{
			include_once __DIR__ . _DS_ . 'exception' . _DS_ . 'FormatParserException.php';
			include_once __DIR__ . _DS_ . 'FormatParserBase.php';
			include_once __DIR__ . _DS_ . 'Dsv.php';
			include_once __DIR__ . _DS_ . 'Csv.php';
			include_once __DIR__ . _DS_ . 'Psv.php';
			// include_once __DIR__ . _DS_ . 'Json.php';
			include_once __DIR__ . _DS_ . 'Tsv.php';
		}
			
		public static function registerBehaviours()
		{
				
		}
	
		public function init()
		{
				
		}
	
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
				$error_msg = "Engine $engine isn't supported.";
				Nutshell::getInstance()->plugin->Logger->fatal($error_msg); // just to be sure that the error message will be in the log.
				throw new NutshellException($error_msg);
			}
		}
	}	
}
