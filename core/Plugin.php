<?php
namespace nutshell\core
{
	abstract class Plugin extends Component
	{
		public function __construct()
		{
			parent::__construct();
			if (method_exists($this,'init'))
			{
				$args=func_get_args();
				call_user_func_array(array($this,'init'),$args);
			}
		}
	}
}
?>