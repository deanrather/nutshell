<?php
namespace nutshell\core\loader
{
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
			static::load(array(
			));
		}
		
		private function doLoad($key,Array $args=array())
		{
			//Is the {$this->container} object loaded?
			if (!isset($this->loaded[$this->container][$key]))
			{
				//No, so we need to load all of it's dependancies and initiate it.
				
				#Load TODO: Fully load everything.
				require(NS_HOME.$this->container._DS_.lcfirst($key)._DS_.$key.'.php');
				
				//Construct the class name.
				$className='nutshell\\'.$this->container.'\\'.lcfirst($key).'\\'.$key;
				$className::loadDependencies();
				
				#Initiate
				$this->loaded[$this->container][$key]=$className::getInstance($args);
			}
			return $this->loaded[$this->container][$key];
		}
		
		public function __invoke($container)
		{
			$this->container=$container;
			return $this;
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