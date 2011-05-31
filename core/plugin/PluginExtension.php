<?php
namespace nutshell\core\plugin
{
	use nutshell\core\exception\Exception;
	use nutshell\core\Component;
	use nutshell\helper\Object;
	
	abstract class PluginExtension extends Component
	{
		public static function register(){}
		
		public $config	=null;
		
		public function __construct()
		{
			$this->config=$this->core->config->plugin->{Object::getBaseClassName($this->getParentPlugin())};
		}
		
		private function getParentPlugin()
		{
			$NS=Object::getNamespace($this);
			$NSParts=explode('\\',$NS,4);
			array_pop($NSParts);
			$NSParts[2]=ucwords($NSParts[2]);
			return implode('\\',$NSParts);
		}
	}
}
?>