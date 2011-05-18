<?php
namespace nutshell\core\config
{
	require_once __DIR__ . '/../Exception.php';
	
	use nutshell\core\Exception;
	
	/**
	 * Configuration node instance
	 * 
	 * @author guillaume
	 *
	 */
	class Config
	{
		const PARSEABLE_CLASS = 'stdClass';
		
		protected $data = null;
		
		/**
		 * Constructor. Loads the supplied object in the current instance.
		 * 
		 */
		protected function __construct($obj) 
		{
			$this->load($obj);
		}
		
		/**
		 * Loads the supplied object into the current config instance
		 * 
		 * @param unknown_type $obj
		 * @throws Exception if the argument is not of a supported type
		 */
		protected function load($obj) {
			$this->data = array();
			
			if(is_object($obj) && get_class($obj) == self::PARSEABLE_CLASS)  
			{
				//convert the argument to an array to iterate simply over it
				$array = (array) $obj;
				
				foreach($array as $k => $v) {
					$this->data[$k] = self::parse($v);
				}
				
				return $this;
			}
			throw new Exception(sprintf('Could not parse argument into a config object: not an instance of %s', self::PARSEABLE_CLASS));
		}
		
		/**
		 * Generic method to transform a random block of data into a config object or value. 
		 * 
		 * @param unknown_type $obj
		 * @param boolean $isRoot
		 */
		public static function parse($obj)
		{
			if (is_object($obj)) 
			{
				return new Config($obj);
			} 
			else 
			{
				return $obj;
			} 
		}
		
		/**
		 * 
		 * 
		 * @param unknown_type $config
		 */
		public function extendWith($config)
		{
			return $this;
		}
		
		/**
		 * Magic getter
		 * 
		 * @param unknown_type $key
		 */
		public function __get($key) 
		{
			if (array_key_exists($key, $this->data))
			{
				return $this->data[$key];
			}
			return null;
		}
		
		/**
		 * Magic setter
		 * 
		 * @param unknown_type $key
		 * @param unknown_type $value
		 */
		public function __set($key, $value) 
		{
			$this->data[$key] = $value;
		}
	}
}