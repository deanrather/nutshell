<?php
namespace nutshell\plugin\direct
{
	use nutshell\plugin\mvc\Controller;
	use nutshell\core\exception\Exception;
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	
	class Direct extends Plugin implements Native,Singleton
	{
		private $services=array();
		
		public static function loadDependencies()
		{
			require(__DIR__.'/ProviderController.php');
			require(__DIR__.'/Service.php');
		}
		
		public function init()
		{
			
		}
		
		public function initService(Controller $controller,$APIRef)
		{
			if (isset($this->config->{$APIRef}))
			{
				if (!isset($this->services[$APIRef]))
				{
					return $this->services[$APIRef]=new Service($controller,$APIRef);
				}
				else
				{
					return $this->services[$APIRef];
				}
			}
			else
			{
				throw new Exception('Invalid API Reference. "'.$APIRef.'" has not been defined in config.');
			}
		}
	}
}
?>