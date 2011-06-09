<?php
namespace nutshell\plugin\logger\writer\db
{
	use nutshell\plugin\logger\exception\LoggerException;
	use nutshell\plugin\logger\writer\Writer;
	
	class DbWriter extends Writer
	{
		protected $connector = null;
		
		protected $activeConnector = null;
		
		protected $table = null;
		
		const CONNECTION_TEST = 'SELECT 1';
		
		const TABLE_EXISTENCE_TEST = 'SELECT 1';
		
		protected function parseConfig(Config $config)
		{
			parent::parseConfig($config);
			
			$this->parseConfigOption($config, 'connector');
			$this->parseConfigOption($config, 'table');
		}
		
		protected function doWrite($msg)
		{
			try
			{
				$this->plugin->Db->insert();
			}
			catch(\Exception $e)
			{
				throw LoggerException(sprintf("Could not log message %s", $msg), 0, $e);
			}
			
		}
	}
}