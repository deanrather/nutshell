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
		 * @param mixed $obj
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
		 * @param mixed $obj
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
		 * @param nutshell\config\Config $config
		 */
		public function extendWith($config)
		{
			return $this;
		}
		
		/**
		 * Magic getter
		 * 
		 * @param String $key
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
		 * @param String $key
		 * @param mixed $value
		 */
		public function __set($key, $value) 
		{
			$this->data[$key] = $value;
		}
		
		/**
		 * Render primitive types
		 * 
		 * @param mixed $data
		 */
		protected function primitiveRendering($data) 
		{
			return json_encode($data);
		}
		
		/**
		 * __toString handler
		 * 
		 * @return string a representation of the instance
		 * 
		 */
		public function __toString() 
		{
			$out = "{";
			$separator = '';
			
			foreach($this->data as $key => $value) {
				$out .= sprintf('%s"%s":%s',
					$separator,
					$key, 
					$value instanceof Config ? $value->__toString() : $this->primitiveRendering($value)
				);
				
				//set the separator
				$separator = ',';
			}
			$out .= "}";
			return $out;
		}
		
		public function prettyPrint()
		{
			$json = $this->__toString();
			$tab = "  ";
			$new_json = "";
			$indent_level = 0;
			$in_string = false;
		
			$json_obj = json_decode($json);
		
			if($json_obj === false)
				return false;
		
			$json = json_encode($json_obj);
			$len = strlen($json);
		
			for($c = 0; $c < $len; $c++)
			{
				$char = $json[$c];
				switch($char)
				{
					case '{':
					case '[':
						if(!$in_string)
						{
							$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
							$indent_level++;
						}
						else
						{
							$new_json .= $char;
						}
						break;
					case '}':
					case ']':
						if(!$in_string)
						{
							$indent_level--;
							$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
						}
						else
						{
							$new_json .= $char;
						}
						break;
					case ',':
						if(!$in_string)
						{
							$new_json .= ",\n" . str_repeat($tab, $indent_level);
						}
						else
						{
							$new_json .= $char;
						}
						break;
					case ':':
						if(!$in_string)
						{
							$new_json .= ": ";
						}
						else
						{
							$new_json .= $char;
						}
						break;
					case '"':
						if($c > 0 && $json[$c-1] != '\\')
						{
							$in_string = !$in_string;
						}
					default:
						$new_json .= $char;
						break;                   
				}
			}
		
			return $new_json;
		}
	}
}