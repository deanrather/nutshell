<?php
namespace nutshell\core
{
	class Plugin
	{
		public static $required=array();
		
		public function __construct()
		{
			if (method_exists($this,'init'))
			{
				$args=func_get_args();
				call_user_func_array(array($this,'init'),$args);
			}
		}
	}
}
?>