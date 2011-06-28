<?php
namespace nutshell\plugin\router
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\plugin\router\handler\Simple;
	use nutshell\plugin\router\handler\Advanced;
	
	class Router extends Plugin implements Native,Singleton
	{
		const MODE_SIMPLE	='simple';
		const MODE_ADVANCED	='advanced';
		
		private $handler=null;
		
		public static function loadDependencies()
		{
			require(__DIR__.'/Route.php');
			require(__DIR__.'/handler/Simple.php');
			require(__DIR__.'/handler/Advanced.php');
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			//Handle simple routing.
			if ($this->config->mode==self::MODE_SIMPLE)
			{
				$this->handler=new Simple();
			}
			//Handle advanced routing.
			elseif ($this->config->mode==self::MODE_ADVANCED)
			{
				$this->handler=new Advanced();
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
	}
}
?>