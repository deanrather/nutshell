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
	use nutshell\core\loader\Loader;
	use nutshell\plugin\router\Router;
	use nutshell\core\exception\NutshellException;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Mvc extends Plugin implements Native,Singleton
	{
		private $modelLoader	=null;
		private $controller		=null;
		private $router			=null;
		private $route			=null;
		
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
			//Setup a model loader.
			$this->modelLoader=new Loader();
			
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
			if (method_exists($this->controller,$this->route->getAction())
			|| method_exists($this->controller,'__call'))
			{
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
			else
			{
				throw new NutshellException('MVC Exception. Unable to execute action. The action "'.$this->route->getAction().'" is not defined or is not a public method.');
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
					$indexFile	=$dir.$controller.'Index.php';
					$file		=$dir.$controller.'.php';
					
					if (is_file($indexFile))
					{
						$this->route->setControl('index');
						$file = $indexFile;
						break;
					}
				}
				else
				{
					throw new NutshellException('MVC Exception. Unable to load controller. The controller "'.$this->route->getControl().'" was not found.');
				}
			}
			include($file);
		}
		
		public function getModelLoader()
		{
			return $this->modelLoader;
		}
	}
}
?>