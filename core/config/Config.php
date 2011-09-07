<?php
/**
* @package nutshell
* @author Guillaume Bodi <guillaume@spinifexgroup.com>
*/
namespace nutshell\core\config
{
	use nutshell\core\config\exception\ConfigException;
	use nutshell\core\Component;
	use \Iterator;
	
	/**
	 * Configuration node instance
	 * 
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell
	 */
	class Config extends Component implements Iterator
	{
		/**
		 * 
		 */
		public static function register()
		{
			static::load(array(
				'ConfigRoot.php'
			));
		}
		
		/**
		 * Name of the default base config file to open if no environment is specified.
		 * 
		 * @var String
		 */		
		const DEFAULT_ENVIRONMENT = 'default';
		
		/**
		 * Label of the config folder on disk
		 * 
		 * @var String
		 */
		const CONFIG_FOLDER = 'config';
		
		/**
		 * Config file extension
		 * 
		 * @var String
		 */
		const CONFIG_FILE_EXTENSION = 'json';
		
		/**
		 * Class that can be handled by the parser.
		 * 
		 * @var String
		 */
		const PARSEABLE_CLASS = 'stdClass';

		/**
		 * Cache for the core config path
		 * 
		 * @var String
		 */
		protected static $default_core_config_path = null;
		
		
		
		protected $data = null;
		
		/**
		 * Constructor. Loads the supplied object in the current instance.
		 * 
		 */
		public function __construct($obj = null) 
		{
			$this->loadObject($obj);
		}
		
		/**
		 * Loads the supplied object into the current config instance
		 * 
		 * @param mixed $obj
		 * @return nutshell\core\config\Config the current instance
		 * @throws Exception if the argument is not of a supported type
		 */
		protected function loadObject($obj) 
		{
			$this->data = array();
			
			if(is_object($obj) && get_class($obj) == self::PARSEABLE_CLASS)  
			{
				//convert the argument to an array to iterate simply over it
				$array = (array) $obj;
				
				foreach($array as $k => $v) 
				{
					$this->data[$k] = self::parse($v, null, null);
				}
				
				return $this;
			}
			else if ($obj  === null) {
				//do nothing, just return
				return $this;
			}
			
			throw new ConfigException(sprintf('Could not parse argument into a config object: not an instance of %s', self::PARSEABLE_CLASS), ConfigException::CODE_NON_PARSEABLE_STRUCTURE);
		}
		
		/**
		 * Generic method to transform a random block of data into a config object or value. 
		 * 
		 * @param mixed $obj
		 * @return mixed the value to be associated to a property
		 */
		public static function parse($obj, $extendHandler, &$extended)
		{
			if (is_object($obj)) 
			{
				return new Config($obj);
			}
			else if (is_array($obj))
			{
				$res = array();
				foreach($obj as $k => $v)
				{
					$res[$k] = self::parse($v, null, null);
				}
				return $res;
			}
			else 
			{
				return $obj;
			} 
		}
		
		/**
		 * Extends the current node with either a Config node or the config node built from a config file specified by path. 
		 * 
		 * @param mixed Config object or file path (String)
		 * @param String optional, the environment to load (used with the file path argument)
		 * @return nutshell\core\config\Config the updated config node (technically $this)
		 */
		public function extendWith($first_arg, $sec_arg = null) 
		{
			if($first_arg instanceof Config)
			{
				//use the specialised method
				$this->extendWithNode($first_arg);
			}
			else
			{
				$this->extendFromPath($first_arg, $sec_arg);
			}
			
			return $this;
		}
		
		/**
		 * Specialised method to extend the current node with another Config node.
		 * 
		 * @param nutshell\core\config\Config $config
		 */
		protected function extendWithNode(Config $config)
		{
			foreach($config as $property => $value) 
			{
				
				if(!$this->hasKey($property)) 
				{
					//add the whole subtree to the current instance
					$this->data[$property] = $config->{$property};
				} 
				else 
				{
					//the property exists in both the original and extended node
					
					//if they are both config objects, propagate
					if($this->{$property} instanceof Config && $config->{$property} instanceof Config) 
					{
						$this->{$property}->extendWith($config->{$property});
					}
					elseif (!($this->{$property} instanceof Config) && !($config->{$property} instanceof Config))
					{
						$this->{$property} = $config->{$property};
					}
					else
					{
						throw new ConfigException(sprintf('Could not extend config object: trying to extend/overwrite a Config node'), ConfigException::CODE_INVALID_NODE_EXTENSION);
					} 
				}
			}
			return $this;
		}
		
		/**
		 * Specialised method to extend the current node with a Config node built from the specified path.
		 * 
		 * @param String $configPath
		 * @param String $environment
		 */
		protected function extendFromPath($alternatives, $environment = null)
		{
			$useDefaultEnvironment = true;
			
			foreach($alternatives as $alternativePath)
			{
				$file = $alternativePath . _DS_ . self::makeConfigFileName($environment);
				if (is_file($file) && is_readable($file))
				{
					$useDefaultEnvironment = false;
					break;
				}
			}
			
			//the specified environment was not found, so try to load the default environment config
			if($useDefaultEnvironment) 
			{
				$environment = self::DEFAULT_ENVIRONMENT;
			}
			
			//extend the current node with the loaded config
			return $this->extendWithNode(self::loadConfigFile($alternatives, self::makeConfigFileName($environment)));
		}
		
		/**
		 * Derives the config file base name from the environment. 
		 * @param String $environment
		 * @return String the config file base name
		 */
		public static function makeConfigFileName($environment)
		{
			return sprintf("%s.%s", $environment, self::CONFIG_FILE_EXTENSION);
		}
		
		/**
		 * Checks whether the config node has a specific key/property 
		 * @param String $key
		 * @return boolean true if said key exists, false otherwise
		 */
		public function hasKey($key) 
		{
			return array_key_exists($key, $this->data);
		}
		
		/**
		 * Magic getter
		 * 
		 * @param String $key
		 * @return mixed the value of the specified property
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
		 * Magic isset
		 * 
		 * @param unknown_type $key
		 */
		public function __isset($key)
		{
			return isset($this->data[$key]);
		}
		
		/**
		 * Render primitive types
		 * 
		 * @param mixed $data
		 * @return String a JSON string representation of the primitive
		 */
		protected static function primitiveRendering($data) 
		{
			if(is_array($data))
			{
				//special handling for arrays
				return self::arrayRendering($data);
			} 
			else 
			{
				return json_encode($data);
			}
		}
		
		/**
		 * 
		 * 
		 * @param array $data
		 * @return String a JSON string representation of the array
		 */
		protected static function arrayRendering(array $data)
		{
			$buff  = '[';
			foreach($data as $i => $v)
			{
				if($i > 0)
				{
					$buff .= ',';
				}
				if (is_object($v) && ($v instanceof Config))
				{
					$buff .= $v->__toString();
				}
				else 
				{
					$buff .= self::primitiveRendering($v);
				}
			}
			$buff .= ']';
			return $buff;
		}
		
		/**
		 * __toString handler
		 * 
		 * @return String a JSON string representation of the instance
		 * 
		 */
		public function __toString() 
		{
			$out = "{";
			$separator = '';
			
			foreach($this->data as $key => $value) 
			{
				$out .= sprintf('%s"%s":%s',
					$separator,
					$key, 
					$value instanceof Config ? $value->__toString() : self::primitiveRendering($value)
				);
				
				//set the separator
				$separator = ',';
			}
			$out .= "}";
			return $out;
		}
		
		/**
		 * Pretty prints the subtree starting from this config node
		 * 
		 * @return String a formatted version of the json tree
		 */
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
		
		/*** [START Implementation of Iterator] ***/
	
		private $it_current_key = null;
		
		private $it_keys = array();
		
		private $it_position = 0;
		
		public function current()
		{
			return $this->data[$this->it_current_key];
		}
		
		public function key()
		{
			return $this->it_current_key;
		}
		
		public function next()
		{
			$this->it_position++;
			$this->it_current_key = 
				array_key_exists($this->it_position, $this->it_keys) 
				? $this->it_keys[$this->it_position] : null;
			return $this;
		}
		
		public function rewind()
		{
			$this->it_keys = array_keys($this->data);
			$this->it_position = 0;
			$this->it_current_key = count($this->it_keys) ? $this->it_keys[$this->it_position] : null;
			return $this;
		}
		
		public function valid()
		{
			return !is_null($this->it_current_key);
		}
		
		/*** [END Implementation of Iterator] ***/
		
		/*** Loader part ***/
		
		public static function loadConfigFile($alternatives, $basename, $extendHandler = null, &$extended = null)
		{ 
			if (!is_array($alternatives))
			{
				$alternatives = array($alternatives);
			}
			
			if($alternatives)
			{
				foreach($alternatives as $pathAlternative)
				{
					$file = $pathAlternative . _DS_ . $basename;
					if(is_file($file) && is_readable($file)) 
					{
						return self::doLoadConfigFile($alternatives, $file);
					}
				}
			}
			
			throw new ConfigException(sprintf("Failed to find a suitable config file named %s", $basename), ConfigException::CODE_CONFIG_FILE_NOT_FOUND);
		}
		
		/**
		 * Loads the specified file into a config instance
		 * 
		 * @param string $file
		 * @throws Exception if the file could not be found/read
		 * @return nutshell\config\Config an instance of the config
		 */
		protected static function doLoadConfigFile($alternatives, $file, $extendHandler = null, &$extended = null) 
		{
			if(is_null($extendHandler)) 
			{
				$scope=get_called_class();
				$extendHandler = function($environment, &$extended) use ($scope, $alternatives)
				{
					return $scope::loadConfigFile(
						$alternatives,
						$scope::makeConfigFileName($environment),
						null,
						$extended
					);
				};
			}
			if(is_file($file)) 
			{
				if(is_readable($file)) 
				{
					if ($extended === null) 
					{
						$extended = array();
					}
					$extended[] = basename($file, '.' . self::CONFIG_FILE_EXTENSION);
					
					//reading the JSON document
					$decodedJSON = json_decode(file_get_contents($file));
					if($decodedJSON === null) 
					{
						throw new ConfigException(sprintf('Invalid JSON document: %s', $file), ConfigException::CODE_INVALID_JSON);
					}
					return ConfigRoot::parse($decodedJSON, $extendHandler, $extended);
				} 
				else
				{
					throw new ConfigException(sprintf('File is not accessible for reading: %s', $file), ConfigException::CODE_INVALID_CONFIG_FILE);
				}  
			} 
			else 
			{
				throw new ConfigException(sprintf('Could not find file: %s', $file), ConfigException::CODE_INVALID_CONFIG_FILE);
			}
		}
	}
}