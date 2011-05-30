<?php
namespace nutshell\core\plugin
{
	use nutshell\Nutshell;
	use nutshell\core\exception\Exception;
	use nutshell\core\Component;
	use nutshell\core\config\Config;
	use nutshell\helper\Object;
	
	abstract class Plugin extends Component
	{
		private static $PLUGIN_CONFIG_LOADED = array();
		
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
				//gets the list of implemented interfaces
				if($interfaces = class_implements($className, false))
				{
					self::loadPluginConfig($className);
					//Is it a Factory?
					if (in_array('nutshell\behaviour\Factory', $interfaces))
					{
						$instance=new static();
						call_user_func_array(array($instance,'init'),$args);
						return $instance;
					}
					//Is it a singleton?
					else if (in_array('nutshell\behaviour\Singleton', $interfaces))
					{
						$instance=new static();
						$GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className]=$instance;
						call_user_func_array(array($instance,'init'),$args);
					}
					//Is it an AbstractFactory?
					else if (in_array('nutshell\behaviour\AbstractFactory', $interfaces))
					{
						return call_user_func_array(array($className,'runFactory'),$args);
					}
	//				//Is it abstractable?
	//				else if ($instance instanceof Abstractable)
	//				{
	//					//TODO: Deal with Abstractable interface.
	//					throw new Exception('TODO: Deal with Abstractable interface.');
	//				}
					else
					{
						throw new Exception('Invalid plugin. Plugin must implement one of either "Factory", "Singleton" or "Abstractable" interfaces.');
					}
				}
			}
			//It must be a singleton. Return the instance.
			return $GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className];
		}
		
		/**
		 * Loads the plugin's configuration into the Nutshell config node.
		 * 
		 * @param String $pluginName
		 */
		public static function loadPluginConfig($pluginClassName)
		{
			if(isset(self::$PLUGIN_CONFIG_LOADED[$pluginClassName]))
			{
				//already registered, skip
				return;
			}
			
			//compute the config folder path for the plugin
			$pluginConfigPath = 
				self::getPluginBasePath($pluginClassName)
				. _DS_ 
				. Config::CONFIG_FOLDER 
			;
			
			//create the plugin config node
			$config = new Config();
			$config->extendWith($pluginConfigPath, NS_ENV);
			
			//add the the nutshell config tree
			$base = Object::getBaseClassName($pluginClassName);
			Nutshell::getInstance()->config->plugin->{$base} = $config;
			
			//set the marker
			self::$PLUGIN_CONFIG_LOADED[$pluginClassName] = true;
		}
		
		/**
		 * Compute the config folder path for the plugin to which the specified class belongs to
		 * 
		 * @param String $pluginClassName
		 * @throws Exception
		 * @return String the absolute system path to the plugin's config folder 
		 */
		protected static function getPluginBasePath($pluginClassName)
		{
			$nsSplit = explode('\\', $pluginClassName);
			
			//necessary depth of 4 minimum
			//nutshell\plugin\<plugin_folder>\<loaded_class>
			if(count($nsSplit) < 4)
			{
				throw new Exception(sprintf('Invalid plugin. The namespace does not meet the structural requirement: %s.', $pluginClassName));
			}
			
			return NS_HOME . 'plugin' . _DS_ . $nsSplit[2]; // plugin folder
		}
	}
}
?>