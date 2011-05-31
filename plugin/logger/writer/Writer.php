<?php
namespace nutshell\plugin\logger\writer
{
	use nutshell\core\config\Config; 
	use nutshell\helper\Object;
	use nutshell\core\exception\Exception;
	
	class Writer
	{
		/**
		 * Pattern (using placeholders) for the message output
		 * 
		 * @var String
		 */
		protected $messagePattern = null;
		
		protected static $config = null;
		
		private static $instances = array();
		
		private static $implementations = array(
			'FileWriter' => 'nutshell\plugin\logger\writer\file\FileWriter',
			'DailyFileWriter' => 'nutshell\plugin\logger\writer\file\DailyFileWriter',
			'DbWriter' => 'nutshell\plugin\logger\writer\db\DbWriter',
		);
		
		protected function __construct(Config $config)
		{
			$this->parseConfig($config);
		}
		
		/**
		 * Extracts valuable configuration options from the config node
		 * 
		 * @param Config $config
		 * @throws Exception if a mandatory parameter is missing
		 */
		protected function parseConfig(Config $config)
		{
			$this->parseConfigOption($config, 'messagePattern');
		}
		
		/**
		 * Reads the config for a specified option and loads it into the appropriate member
		 * 
		 * @param Config $config
		 * @param String $optionName
		 * @param boolean $mandatory
		 * @param String $memberName 
		 */
		protected function parseConfigOption(Config $config, $optionName, boolean $mandatory = null, $memberName = null)
		{
			//default override
			if ($memberName === null) 
			{
				$memberName = $optionName;
			}
			if($mandatory === null) 
			{
				$mandatory = true;
			}
			
			//reading and assignment
			if($config->{$optionName})
			{
				$this->{$memberName} = $config->{$optionName};  
			}
			else if($mandatory)
			{
				throw new Exception("Missing mandatory '%' config option for writer", $optionName);
			}
		}
		
		/**
		 * Obtain an instance corresponding to the name and configuration
		 * 
		 * @param String $writerName
		 * @param nutshell\core\config\Config $writerConfig instance configuration details
		 */
		public static function runFactory($writerName, Config $writerConfig)
		{
			if(!array_key_exists($writerName, self::$instances))
			{
				self::$instances[$writerName] = self::createNewInstance($writerConfig);
			}
			
			return self::$instances[$writerName];
		}
		
		/**
		 * Creates and configure a new instance of a Writer based on the specified configuration
		 * 
		 * @param nutshell\core\config\Config $config instance configuration details
		 * @return nutshell\plugin\logger\writer\Writer a new instance
		 */
		private static function createNewInstance(Config $config)
		{
			if(!array_key_exists($config->class, self::$implementations))
			{
				throw new Exception(sprintf('Cannot resolve requested writer implementation: %s', $config->class));
			}
			
			return new self::$implementations[$config->class]($config);
		} 
		
		public function write($msg)
		{
			//TODO
		}
		
		public function __toString()
		{
			return sprintf("%s", Object::getBaseClassName(get_class($this)));
		}
	}
}