<?php
namespace nutshell\core
{
	use nutshell\core\exception\Exception;
	use nutshell\core\Component;
	use nutshell\behaviour\Factory;
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\Abstractable;
	
	abstract class Plugin extends Component
	{
		public static function register() 
		{
			$GLOBALS['NUTSHELL_PLUGIN_SINGLETON']=array();
			static::load(array());
		}
		
		protected static $instance=null;
		
		/**
		 * 
		 * Enter description here ...
		 * 
		 * @param Array $args
		 * @throws Exception
		 */
		public static function getInstance(Array $args=array())
		{
			$className=get_called_class();
			//If $instance is set, then it is definately a singleton.
			if (!isset($GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className]))
			{
				//Create a new instance.
				$instance=new static();
				//Is it a Factory?
				if ($instance instanceof Factory)
				{
					call_user_func_array(array($instance,'init'),$args);
					return $instance;
				}
				//Is it a singleton?
				else if ($instance instanceof Singleton)
				{
					$GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className]=$instance;
					call_user_func_array(array($instance,'init'),$args);
				}
				//Is it abstractable?
				else if ($instance instanceof Abstractable)
				{
					//TODO: Deal with Abstractable interface.
					throw new Exception('TODO: Deal with Abstractable interface.');
				}
				else
				{
					throw new Exception('Invalid plugin. Plugin must implement one of either "Factory","Singleton" or "Abstractable" interfaces.');
				}
			}
			//It must be a singleton. Return the instance.
			return $GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className];
		}
	}
}
?>