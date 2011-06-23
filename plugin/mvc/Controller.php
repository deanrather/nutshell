<?php
namespace nutshell\plugin\mvc
{
	use nutshell\Nutshell;

	abstract class Controller
	{
		public $core	=null;
		public $plugin	=null;
		public $MVC		=null;
		public $view	=null;
		
		public function __construct(Mvc $MVC)
		{
			$this->core		=Nutshell::getInstance();
			$this->plugin	=$this->core->plugin;
			$this->MVC		=$MVC;
			$this->view		=new View($this->MVC);
		}
	}
}
?>