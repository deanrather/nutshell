<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\router
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\plugin\router\handler\Cli;
	use nutshell\plugin\router\handler\Simple;
	use nutshell\plugin\router\handler\SimpleRest;
	use nutshell\plugin\router\handler\Advanced;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Router extends Plugin implements Native,Singleton
	{
		const MODE_CLI			='cli';
		const MODE_SIMPLE		='simple';
		const MODE_SIMPLE_REST	='simpleRest';
		const MODE_ADVANCED		='advanced';
		
		private $handler=null;
		
		public static function loadDependencies()
		{
			require(__DIR__.'/Route.php');
			require(__DIR__.'/handler/abstract/Http.php');
			require(__DIR__.'/handler/Cli.php');
			require(__DIR__.'/handler/Simple.php');
			require(__DIR__.'/handler/SimpleRest.php');
			require(__DIR__.'/handler/Advanced.php');
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			switch($this->config->mode) 
			{
				case self::MODE_SIMPLE:
					//Handle simple routing.
					$this->handler=new Simple();
					break;
				case self::MODE_SIMPLE_REST:
					//Handle simple rest routing.
					$this->handler=new SimpleRest();
					break;
				case self::MODE_ADVANCED:
					//Handle advanced routing.
					$this->handler=new Advanced();
					break;
				case self::MODE_CLI:
					//Handle command line interface routing
					$this->handler=new Cli();
					break;
			}
		}
		
		public function getMode()
		{
			return $this->config->mode;
		}
		
		public function getRoute()
		{
			return $this->handler->getRoute();
		}
		
		public function getRouter()
		{
			return $this->handler;
		}
	}
}
?>