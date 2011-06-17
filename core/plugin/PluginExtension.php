<?php
namespace nutshell\core\plugin
{
	use nutshell\Nutshell;
	use nutshell\core\exception\Exception;
	use nutshell\core\Component;
	use nutshell\helper\Object;
	
	abstract class PluginExtension extends Component
	{
		public static function register(){}
		
		public $config	=null;
		
		public function __construct()
		{
			$this->config=Nutshell::getInstance()->config->plugin->{Object::getBaseClassName($this->getParentPlugin())};
		}
		
		private function getParentPlugin()
		{
			$NS		=Object::getNamespace($this);
			$NSParts=explode('\\',$NS);
			if ($NSParts[1]=='plugin')
			{
				if (isset($NSParts[2]))
				{
					$NSParts[2]=ucwords($NSParts[2]);
				}
				else
				{
					throw new Exception('Unable to find parent plugin.');
				}
				return $NSParts[0].'\\'.$NSParts[1].'\\'.$NSParts[2];
			}
			else
			{
				throw new Exception('Attempted to use PluginExtension outside of plugin context.');
			}
		}
	}
}
?>