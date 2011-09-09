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
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Mvc extends Plugin implements Native,Singleton
	{
		private $controller=null;
		
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
			//Get the route from the rotuer.
			$route=$this->plugin->Router->getRoute();
			
			//Load the controller.
			$this->loadController($route->getControl());
			
			//Construct the namespaced class name.
			$className='application\controller\\'.ucwords($route->getControl());
			
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
				call_user_func_array(array($this->controller,$route->getAction()),$route->getArgs());
			}
			else if ($route->getControl()!=$route->getAction())
			{
				call_user_func_array(array($this->controller,$route->getAction()),$route->getArgs());
			}
		}
		
		public function loadController($controller)
		{
			include(APP_HOME.$this->config->dir->controllers.$controller.'.php');
		}
	}
}
?>