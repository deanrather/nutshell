<?php
/**
 * @package nutshell
 */
namespace nutshell\core\loader
{
	use nutshell\Nutshell;

	use nutshell\helper\Object;

	use nutshell\core\exception\Exception;

	use nutshell\core\Component;
	use nutshell\plugin;
	
	/**
	 * Configuration node instance
	 * 
	 * @author guillaume
	 * @package nutshell
	 */
	class Loader extends Component
	{
		/**
		 * A list of valid containers and their paths.
		 * 
		 * @access private
		 * @var Array
		 */
		private $containers=array();
		
		/**
		 * The current container.
		 * 
		 * This is a pointer to the active container.
		 * 
		 * @access private
		 * @var String
		 */
		private $container	='plugin';
		
		/**
		 * A container of loaded classes.
		 * 
		 * @var Array
		 */
		private $loaded		=array
		(
			'plugin'		=>array()
		);
		
		public static function autoload($className)
		{
			$namespace=Object::getNamespace($className);
			$className=Object::getBaseClassName($className);
			//Check for a plugin behaviour.
			if (strstr($namespace,'behaviour\\'))
			{
				list(,,$plugin)	=explode('\\',$namespace);
				$pathSuffix		='plugin'._DS_.$plugin._DS_.'behaviour'._DS_.$className.'.php';
				if (is_file($file=NS_HOME.$pathSuffix))
				{
					//Invoke the plugin.
					Nutshell::getInstance()->plugin->{ucfirst($plugin)};
				}
				else if (is_file($file=APP_HOME.$pathSuffix))
				{
					Nutshell::getInstance()->plugin->{ucfirst($plugin)};
				}
				else
				{
					throw new Exception('Unable to autoload class "'.$namespace.$className.'".');
				}
			}
		}
		
		/**
		 * 
		 */
		public static function register()
		{
			static::load(array());
		}
		
		public function __construct()
		{
			spl_autoload_register(__NAMESPACE__ .'\Loader::autoload');
		}
		
		
		public function registerContainer($name,$path,$namespace)
		{
			$this->containers[$name]=array
			(
				'path'		=>$path,
				'namespace'	=>$namespace
			);
		}
		
		private function doLoad($key,Array $args=array())
		{
			//Is the {$this->container} object loaded?
			if (!isset($this->loaded[$this->container][$key]))
			{
				//No, so we need to load all of it's dependancies and initiate it.
				
				$dirBase=$this->containers[$this->container]['path'];
				$namespaceBase=$this->containers[$this->container]['namespace'];

				#Load TODO: Fully load everything.
				if (is_file($file=$dirBase.lcfirst($key)._DS_.$key.'.php'))
				{
					require($file);
					//Construct the class name.
					$className=$this->getClassName($key);
				}
				else if (is_file($file=$dirBase.$key.'.php'))
				{
					require($file);
					//Construct the class name.
					$className=$this->getClassName($key);
				}
				// is it a folder with other components?
				else if (is_dir($dir=$dirBase.$key))
				{
					$localContainer=$this->container;
					$localInstance = new Loader;
					$localInstance->registerContainer($key,$dir._DS_,$namespaceBase.$key.'\\');
					$localInstance->setContainer($key);
					$this->loaded[$localContainer][$key]=$localInstance;
					return $localInstance;
				} else
				{
					throw new Exception('Loader failed to load "'.$key.'" from container "'.$this->container.'".');
				}
				if($interfaces = class_implements($className, false))
				{
					//Load class dependencies
					if (in_array('nutshell\behaviour\Native', $interfaces))
					{
						$className::loadDependencies();
						$className::registerBehaviours();
					}
				}
			}
			else
			{
				$localObj = &$this->loaded[$this->container][$key];
				if ( get_class($localObj) == get_class($this) )
				{
					return $localObj;
				} 
				else 
				{
					$className=$this->getClassName($key);
				}
				
			}
			if (!isset($interfaces))
			{
				$interfaces=class_implements($className, false);
			}
			if (in_array('nutshell\behaviour\Loadable', $interfaces))
			{
				#Initiate
				$localContainer=$this->container;
				$localInstance=$className::getInstance($args);
				$this->loaded[$localContainer][$key]=$localInstance;
				return $localInstance;
			}
			else
			{
				throw new Exception('Loader failed to load class "'.$className.'". This is likely because the container '
									.'handle you\'re using to handle the loading with doesn\'t impement the "Loadable" behaviour.');
			}
		}
		
		private function getClassName($key)
		{
			$dirBase=$this->containers[$this->container]['path'];
			if (is_file($dirBase.lcfirst($key)._DS_.$key.'.php'))
			{
				return $this->containers[$this->container]['namespace'].lcfirst($key).'\\'.$key;
			}
			else if (is_file($dirBase.$key.'.php'))
			{
				return $this->containers[$this->container]['namespace'].$key;
			}
		}
		
		public function __invoke($container)
		{
			if (isset($this->containers[$container]))
			{
				$this->container=$container;
				return $this;
			}
			else
			{
				throw new Exception('Invalid container. Container "'.$container.'" has not been registered.');
			}
		}
		
		public function setContainer($pContainer)
		{
			$this->container=$pContainer;
		}
		
		public function __get($key)
		{
			return $this->doLoad($key);
		}
		
		public function __call($key,$args)
		{
			return $this->doLoad($key,$args);
		}
	}
}