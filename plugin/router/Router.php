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
	use nutshell\plugin\router\exception\RouterException;
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
		
		private static $handlers = array();
		
		public static function loadDependencies()
		{
			require(__DIR__.'/Route.php');
			require(__DIR__.'/exception/RouterException.php');
			require(__DIR__.'/handler/abstract/Http.php');
			require(__DIR__.'/handler/Cli.php');
			require(__DIR__.'/handler/Simple.php');
			require(__DIR__.'/handler/SimpleRest.php');
			require(__DIR__.'/handler/Advanced.php');
			
			//try to load user handlers
			$userHandlerDir = APP_HOME . 'plugin' . _DS_ . 'router' . _DS_ . 'handler'; 
			if(is_dir($userHandlerDir)) {
				if($userHandlers = glob($userHandlerDir . _DS_ . '*.php')){
					foreach($userHandlers as $userHandler) {
						require_once($userHandler);
					}
				}
			}
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public static function registerHandler($key, $class) {
			if(!isset(self::$handlers[$key])) 
			{
				self::$handlers[$key] = $class;
			}
			else 
			{
				throw new RouterException(sprintf("A handler is already registered for the key: %s (%s)", $key, self::$handlers[$key]));
			}
		}
		
		public function init()
		{
			//reads the mode from the config file and try to mach
			if(!isset(self::$handlers[$this->config->mode])) {
				throw new RouterException(sprintf('No router mode configuration could be found or the selected mode does not match any of the available handlers: %s', $this->config->mode));
			}
			
			//loads the class name
			$handlerClass = self::$handlers[$this->config->mode];
			
			//checks that a class suitably named has been loaded
			if(!class_exists($handlerClass)) {
				throw new RouterException(sprintf('No implementation could be found for Router handler class: %s', $handlerClass));
			}
			
			//initialise the handler
			$this->handler = new $handlerClass();
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