<?php
namespace nutshell\plugin\mvc
{
	use nutshell\core\plugin\PluginExtension;
	
	abstract class Model extends PluginExtension
	{
		public function __construct()
		{
			parent::__construct();
			//TODO: Make this namespace configurable.
			$this->core->getLoader()->registerContainer('model','application\model\\');
		}
	}
}
?>