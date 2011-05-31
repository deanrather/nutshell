<?php
namespace nutshell\plugin\logger\writer\file
{
	use nutshell\plugin\logger\writer\Writer;
	use nutshell\core\config\Config;
	
	class FileWriter extends Writer
	{
		protected $output = null;

		protected function parseConfig(Config $config)
		{
			parent::parseConfig($config);
			
			$this->parseConfigOption($config, 'output');
		}
	}
}