<?php
namespace nutshell\plugin\router
{
	use nutshell\core\plugin\PluginExtension;

	class Route
	{
		private $control=null;
		private $action	=null;
		private $args	=array();
		
		public function __construct($control,$action,$args)
		{
			$this->control	=$control;
			$this->action	=$action;
			$this->args		=$args;
		}
		
		public function getControl()
		{
			return $this->control;
		}
		
		public function getAction()
		{
			return $this->action;
		}
		
		public function getArgs()
		{
			return $this->args;
		}
		
		public function getArg($num)
		{
			if (isset($this->args[$num]))
			{
				return $this->args[$num];
			}
			return null;
		}
	}
}
?>