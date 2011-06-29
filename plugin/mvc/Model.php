<?php
namespace nutshell\plugin\mvc
{
	use nutshell\core\plugin\PluginExtension;
	use nutshell\behaviour\Loadable;
	
	abstract class Model implements Loadable
	{
		public function __construct()
		{
			
		}
		
		public static function getInstance(Array $args=array())
		{
			$className=get_called_class();
			if (!isset($GLOBALS['NUTSHELL_MODEL'][$className]))
			{
				$instance=new static();
				if (method_exists($instance,'init'))
				{
					call_user_func_array(array($instance,'init'),$args);
				}
				$GLOBALS['NUTSHELL_MODEL'][$className]=$instance;
			}
			return $GLOBALS['NUTSHELL_MODEL'][$className];
		}
	}
}
?>