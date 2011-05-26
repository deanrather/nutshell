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
			static::load(array());
		}
		
		private static $instance=null;
		
		public static function getInstance($args=null)
		{
			//If $instance is set, then it is definately a singleton.
			if (is_null(self::$instance))
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
					self::$instance=$instance;
					call_user_func_array(array(self::$instance,'init'),$args);
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
			return self::$instance;
		}
	}
}
?>