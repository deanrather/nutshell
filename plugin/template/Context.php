<?php
namespace nutshell\plugin\template
{
	use nutshell\core\exception\Exception;
	use nutshell\core\plugin\PluginExtension;
	use \Closure;
	
	class Context extends PluginExtension
	{
		private $keyVals	=array();
		private $callbacks	=array();
		
		public function __construct(Array &$keyVals)
		{
			$this->setKeyVals($keyVals);
		}
		
		public function setKeyVals(Array &$keyVals)
		{
			$this->keyVals=$keyVals;
		}
		
		public function get($key)
		{
			if (isset($this->keyVals[$key]))
			{
				return $this->keyVals[$key];
			}
			return null;
		}
		
		public function __get($key)
		{
			if (isset($this->keyVals[$key]))
			{
				print $this->keyVals[$key];
			}
			print '';
		}
		
		public function registerCallback($name,Closure $closure)
		{
			$this->callbacks[$name]=$closure;
			return $this;
		}
		
		public function __call($method,$args)
		{
			if (isset($this->callbacks[$method]))
			{
				call_user_func_array($this->callbacks[$method],$args);
			}
			else
			{
				throw new Exception('Invalid template function. Function "'.$method.'" hsa not been registered. Register with $context->registerCallback($name,$closure).');
			}
		}
	}
}
?>