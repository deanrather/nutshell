<?php
namespace nutshell\core
{
	abstract class Component
	{
		abstract public static function register();
		
		public static function load($files)
		{
			$namespace = preg_replace('/\\\\[^\\\\]*$/', '', get_called_class());
			
			if (!is_array($files))$files=array($files);
			for ($i=0,$j=count($files); $i<$j; $i++)
			{
				$file=NS_HOME.str_replace
				(
					array('nutshell\\','\\'),
					array('',_),
					$namespace
				)._.$files[$i];
				require($file);
			}
		}
	}
}
?>