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
	
	/**
	 * 
	 * @abstract
	 */
	abstract class Plugin extends Component implements Loadable
	{
		/**
		 * @access public
		 * @static
		 * @var Array
		 */
		private static $PLUGIN_CONFIG_LOADED	= array();
		/**
		 * @access protected
		 * @static
		 * @var Object
		 */
		protected static $instance				= null;
		/**
		 * @access protected
		 * @static
		 * @var Array
		 */
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
		
		/**
		 * Class Constructor. Creates plugin shortcuts to commonly
		 * used by plugins.
		 * 
		 * @access private
		 * @return nutshell\core\plugin\Plugin
		 */
		public function __construct()
		{
			$this->core=	Nutshell::getInstance();
			$this->config=	$this->core->config->plugin->{Object::getBaseClassName($this)};
			$this->plugin=	Nutshell::getInstance()->plugin;
		}
		
		/**
		 * 
		 * @see nutshell\core\Component
		 */
		public static function register() 
		{
			$GLOBALS['NUTSHELL_PLUGIN_SINGLETON']=array();
			static::load(array());
		}
		
		/**
		 * A magic method for fetching an instance of this plugin's config block.
		 * 
		 * @access public
		 * @static
		 * @return nutshell\core\config\Config
		 */
		public static function config()
		{
			return Nutshell::getInstance()->config->plugin->{Object::getBaseClassName(get_called_class())};
		}
		
		/**
		 * Register's a custom behavior with attached functionality.
		 * 
		 * You would usually attach a closure to this method as the callback and embed
		 * functionality which performs the specific "behaviour".
		 * 
		 * The closure must accept a single parameter "$classInstance" which is the instance
		 * of the class which has implemented the custom behaviour.
		 * 
		 * @access protected
		 * @static
		 * @param Mixed $plugin - Either the plugin name or an instance of the plugin.
		 * @param String $behaviourName - The name of the behaviour to register.
		 * @param Mixed $callback - A function reference or Closure.
		 * @return Void
		 * @throws Exception
		 */
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
		 * This method is used internally by {@link nutshell\core\loader\Loader} to
		 * create/return instances of plugins. This almost always happens when
		 * something attempts to access the plugin via a plugin shortcut since these
		 * always point to the Loader's plugin container.  
		 * 
		 * @internal
		 * @access public
		 * @static
		 * @param Array $args - Args to be passed to the plugin constructor.
		 * @throws Exception - If one of the base behaviours is not implemented.
		 * @return nutshell\core\plugin\Plugin
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
						if (!count($args))$args[0]=null;
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
//						throw new Exception('Invalid plugin. Plugin must implement one of either "Factory", "Singleton" or "Abstractable" interfaces.');
						throw new Exception('Invalid plugin. Plugin must implement one of either "Factory", "Singleton" or "AbstractFactory" interfaces.');
					}
				}
			}
			//It must be a singleton. Return the instance.
			return $GLOBALS['NUTSHELL_PLUGIN_SINGLETON'][$className];
		}
		
		/**
		 * This is an internal method which is responsible for applying a custom behaviour
		 * to a plugin which has implemented said behavior by executing the registered 
		 * behaviour's callback method and passing through the instance of the plugin.
		 * 
		 * @internal
		 * @access public
		 * @static
		 * @param nutshell\core\plugin\Plugin $classInstance - The instance of the class which implemented the behavior.
		 * @return Void
		 */
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
		
		/**
		 * This overloader is responsible for executing {@link nutshell\behaviour\AbstractFactory::runFactory()}
		 * on plugins which implement a behaviour combination of {@link nutshell\behaviour\Singleton}
		 * and {@link nutshell\behaviour\AbstractFactory}
		 * 
		 * This magic basically allows the logic of creating instances of 
		 * plugins to be offloaded to the plugin's implementation of runFactory().
		 * 
		 * @param String $key
		 */
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