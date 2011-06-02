<?php
namespace nutshell\plugin\logger\writer\file
{
	use nutshell\core\config\Config;
	
	use nutshell\core\exception\Exception;
	
	use nutshell\plugin\logger\writer\Writer;
	
	
	class FileWriter extends Writer
	{
		protected $output = null;
		
		protected function parseConfig(Config $config)
		{
			parent::parseConfig($config);
			
			$this->parseConfigOption($config, 'output');
			$this->validateOutput();
		}
		
		/**
		 * 
		 * @param unknown_type $path
		 */
		protected function resolveOutputPlaceHolders($path)
		{
			return str_replace(
				array('{NS_HOME}'),
				array(NS_HOME),
				$path
			);
		}
		
		/**
		 * Validate and flatten the path to the log file 
		 * 
		 * @throws Exception if the log file could not be located or created
		 */
		protected function validateOutput()
		{
			$realPath = $this->resolveOutputPlaceHolders($this->output);
			
			if(!file_exists($realPath))
			{
				if(!touch($realPath)) 
				{
					throw new Exception('Could not create log file at: %s', $realPath);
				}
			}
			else
			{
				if(!is_writable($realPath)) 
			  	{
					throw new Exception('Could not access log file at : %s for writing', $realPath);
			  	}
			}
			
			$this->output = realpath($realPath);
		}
		
		/**
		 * (non-PHPdoc)
		 * @see nutshell\plugin\logger\writer.Writer::doWrite()
		 */
		protected function doWrite($msg)
		{
			file_put_contents($this->output, $msg, FILE_APPEND);
		}
	}
}