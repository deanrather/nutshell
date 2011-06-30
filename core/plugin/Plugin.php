<?php
namespace nutshell\core\plugin
{
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\AbstractFactory;
	use nutshell\behaviour\Loadable;

	use nutshell\Nutshell;
	use nutshell\core\exception\Exception;
	use nutshell\core\Component;
	use nutshell\core\config\Config;
	use nutshell\helper\Object;
	
	abstract class Plugin extends Component implements Loadable
	{
		private static $PLUGIN_CONFIG_LOADED	= array();
		protected static $instance				= null;
		protected static $behaviours			= array();
		
		/**
		 * @access public
		 * @var nutshell\core\config\Config
		 */
		public $config							= null;
		/**
		 * @access public
		 * @var nutshell\Nutshell
		 */
		public $core							= null;
		/**
		 * @access public
		 * @var nutshell\core\plugin\Plugin
		 */
		public $plugin							= null;
		
		public function __construct()
		{
			$this->core=	Nutshell::getInstance();
			$this->config=	$this->core->config->plugin->{Object::getBaseClassName($this)};
			$this->plugin=	Nutshell::getInstance()->plugin;
		}
		
		public static function register() 
		{
			$GLOBALS['NUTSHELL_PLUGIN_SINGLETON']=array();
			static::load(array());
		}
		
		public static function config()
		{
			return Nutshell::getInstance()->config->plugin->{Object::getBaseClassName(get_called_class())};
		}
		
		protected static function registerBehaviour($plugin,$behaviourName,$callback)
		{
			$pluginName	=lcfirst(Object::getBaseClassName($plugin));
			$behaviour	='nutshell\behaviour\\'.$pluginName.'\\'.$behaviourName;
			if (!isset(self::$behaviours[$behaviour]))
			{
				self::$behaviours[$behaviour]=$callback;
			}
			else
			{
				throw new Exception('Plugin ".$pluginName." tried to define the behaviour "'.$behaviourName.'". But this behaviour has already been defined.');
			}
		}
		
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
					//Is it a Factory?
					if (in_array('nutshell\behaviour\Factory', $interfaces))
					{
						$instance=new static();
						self::applyBehaviours($instance);
						call_user_func_array(array($instance,'init'),$args);
						return $instance;
					}
					//Is it a singleton?
					else if (in_array('nutshell\behaviour\Singleton', $interfaces))
					{
						$instance=new static();
						self::applyBehaviours($instance);
						$GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className]=$instance;
						call_user_func_array(array($instance,'init'),$args);
					}
					//Is it an AbstractFactory?
					else if (in_array('nutshell\behaviour\AbstractFactory', $interfaces))
					{
						return call_user_func_array(array($className,'runFactory'),$args);
					}
//					//Is it abstractable?
//					else if ($instance instanceof Abstractable)
//					{
//						//TODO: Deal with Abstractable interface.
//						throw new Exception('TODO: Deal with Abstractable interface.');
//					}
					else
					{
						throw new Exception('Invalid plugin. Plugin must implement one of either "Factory", "Singleton" or "Abstractable" interfaces.');
					}
				}
			}
			//It must be a singleton. Return the instance.
			return $GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className];
		}
		
		public static function applyBehaviours($classInstance)
		{
			$interfaces=class_implements($classInstance,false);
			//Apply behaviours.
			foreach (self::$behaviours as $behaviour=>$callback)
			{
				if (in_array($behaviour,$interfaces))
				{
					$callback($classInstance);
				}
			}
		}
		
		public function __get($key)
		{
			if (($this instanceof Singleton && $this instanceof AbstractFactory)
			&& $result=static::runFactory($key))
			{
				return $result;
			}
			return null;
		}
	}
}
?>