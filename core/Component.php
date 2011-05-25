<?php
namespace nutshell\core
{
	abstract class Component
	{
		abstract public static function register();
		
		public static function load($files)
		{
			if (!is_array($files))$files=array($files);
			for ($i=0,$j=count($files); $i<$j; $i++)
			{
				$file=NS_HOME.str_replace
				(
					array('nutshell\\','\\'),
					array('',_),
					__NAMESPACE__
				).$files[$i];
				require($file);
			}
		}
	}
}
?>