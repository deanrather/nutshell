<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\mvc
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\plugin\router\Router;
	use nutshell\core\exception\NutshellException;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Mvc extends Plugin implements Native,Singleton
	{
		private $controller	=null;
		private $router		=null;
		private $route		=null;
		
		public static function loadDependencies()
		{
			require(__DIR__.'/Http.php');
			require(__DIR__.'/MvcConnect.php');
			require(__DIR__.'/Model.php');
			require(__DIR__.'/View.php');
			require(__DIR__.'/Controller.php');
			require(__DIR__.'/model/CRUD.php');
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			//Get the router handler.
			$this->router	=$this->plugin->Router->getRouter();
			//Get the route from the rotuer.
			$this->route	=$this->plugin->Router->getRoute();
			
			//Load the controller.
			$this->loadController();
			
			//Construct the namespaced class name.
			$className='application\controller\\'.$this->route->getControlNamespace().ucwords($this->route->getControl());
			
			//Initiate the controller.
			$this->controller=new $className($this);
			
			/* A hack to get around PHP4's constructor.
			 * This only applies to version of PHP 5.3 lower than 5.3.3.
			 * 
			 * We don't kick off the action if the action is the same
			 * name as the constructor.
			 * 
			 * 
			 * Note that the default action cannot accept any args.
			 * So nothing is passed to the constructor.
			 */
			if (version_compare(PHP_VERSION,'5.3.3','>='))
			{
				//Call the controller action.
				call_user_func_array(array($this->controller,$this->route->getAction()),$this->route->getArgs());
			}
			else if ($this->route->getControl()!=$this->route->getAction())
			{
				call_user_func_array(array($this->controller,$this->route->getAction()),$this->route->getArgs());
			}
		}
		
		public function loadController()
		{
			$controller	=$this->route->getControl();
			$dir		=APP_HOME.$this->config->dir->controllers;
			$file		=$dir.$controller.'.php';
			while (true)
			{
				if (is_file($file))
				{
					break;
				}
				else if (is_dir($dir))
				{
					$this->router->advancePointer();
					$dir		.=$controller._DS_;
					$controller	=$this->route->getControl();
					$file		=$dir.$controller.'.php';
				}
				else
				{
					throw new NutshellException('Unable to load controller. The controller "'.$this->route->getControl().'" was not found.');
				}
			}
			include($file);
		}
	}
}
?>