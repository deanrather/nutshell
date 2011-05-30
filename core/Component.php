<?php
namespace nutshell\core
{
	use nutshell\Nutshell;

	use nutshell\helper\Object;
	
	abstract class Component
	{
		abstract public static function register();
		
		public static function load($files)
		{
			$directory = Object::getClassPath(get_called_class());
			
			if (!is_array($files))$files=array($files);
			for ($i=0,$j=count($files); $i<$j; $i++)
			{
				$file= $directory . $files[$i];
				require($file);
			}
		}
		
		public function __get($key)
		{
			switch ($key)
			{
				case 'config':	return Nutshell::getInstance()->config;
				case 'core':	return Nutshell::getInstance();
				case 'plugin':	return Nutshell::getInstance()->plugin;
			}
		}
	}
}
?>