<?php
/**
 * @package nutshell-plugin
 * @author Timothy Chandler<tim.chandler@spinifexgroup.com>
 */
namespace nutshell\plugin\storage
{
	use nutshell\core\plugin\Plugin;
	use nutshell\core\exception\Exception;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\AbstractFactory;
	
	/**
	 * @package nutshell-plugin
	 * @author Timothy Chandler<tim.chandler@spinifexgroup.com>
	 */
	class Storage extends Plugin implements Native,AbstractFactory
	{
		const ENGINE_DIR='engine';
		const ENGINE_NAMESPACE='\\engine\\';
		
		
		public static function loadDependencies()
		{
			require_once(__DIR__.'/Engine.php');
			require_once(__DIR__.'/AbstractBucket.php');
			
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public static function runFactory($handler)
		{
			self::loadDependencies();
			$handler=strtolower($handler);
			$file=__DIR__._DS_.self::ENGINE_DIR._DS_.$handler._DS_.ucfirst($handler).'.php';
			if (is_file($file))
			{
				require_once($file);
				$class=__NAMESPACE__ .self::ENGINE_NAMESPACE.$handler.'\\'.ucfirst($handler);
				return new $class;
			}
			else
			{
				throw new Exception('Invalid storage handler "'.$handler.'".');
			}
		}
	}
}
?>