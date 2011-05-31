<?php
namespace nutshell\plugin\template
{
	use nutshell\core\plugin\PluginExtension;
	
	class Context extends PluginExtension
	{
		private $keyVals;
		
		public function __construct($keyVals)
		{
			$this->keyVals=$keyVals;
		}
		
		public function __get($key)
		{
			if (isset($this->keyVals[$key]))
			{
				print $this->keyVals[$key];
			}
			print '';
		}
		
		public function get($key)
		{
			if (isset($this->keyVals[$key]))
			{
				return $this->keyVals[$key];
			}
			return null;
		}
	}
}
?>