<?php
namespace nutshell\core
{
	use nutshell\helper\Object;
	
	abstract class Component
	{
		abstract public static function register();
		
		public static function load($files)
		{
			$namespace = Object::getNamespace(get_called_class());
			
			if (!is_array($files))$files=array($files);
			for ($i=0,$j=count($files); $i<$j; $i++)
			{
				$file=NS_HOME.str_replace
				(
					array('nutshell\\','\\'),
					array('',_DS_),
					$namespace
				)._DS_.$files[$i];
				require($file);
			}
		}
		
		public function __get($key)
		{
			switch ($key)
			{
				case 'config':	return $GlOBALS['NUTSHELL']->config;
				case 'core':	return $GLOBALS['NUTSHELL'];
				case 'plugin':	return $GLOBALS['NUTSHELL']->loader('plugin');
			}
		}
	}
}
?>