<?php
namespace nutshell\core\loader
{
	use nutshell\core\exception\Exception;

	use nutshell\core\Component;
	use nutshell\plugin;
	
	/**
	 * Configuration node instance
	 * 
	 * @author guillaume
	 *
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
		
		/**
		 * 
		 */
		public static function register()
		{
			static::load(array());
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
			//Construct the class name.
			$className=$this->containers[$this->container]['namespace'].lcfirst($key).'\\'.$key;
			//Is the {$this->container} object loaded?
			if (!isset($this->loaded[$this->container][$key]))
			{
				//No, so we need to load all of it's dependancies and initiate it.
				
				#Load TODO: Fully load everything.
				require($this->containers[$this->container]['path'].lcfirst($key)._DS_.$key.'.php');
				
				if($interfaces = class_implements($className, false))
				{
					//Load class dependencies
					if (in_array('nutshell\behaviour\Native', $interfaces))
					{
						$className::loadDependencies();
					}
				}
			}
			#Initiate
			$this->loaded[$this->container][$key]=$className::getInstance($args);
			return $this->loaded[$this->container][$key];
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
				throw new Exception('Invalid container. Containre "'.$container.'" has not been registered.');
			}
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