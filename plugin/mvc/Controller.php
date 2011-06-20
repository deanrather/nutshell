<?php
namespace nutshell\plugin\mvc
{
	abstract class Controller
	{
		public $MVC	=null;
		public $view=null;
		
		public function __construct(Mvc $MVC)
		{
			$this->MVC	=$MVC;
			$this->view	=new View($this->MVC);
		}
	}
}
?>