<?php
namespace nutshell\plugin\db\impl
{
	use \MongoClient;
	use \MongoException;

	use nutshell\plugin\db\exception\DbException;
	
	/**
	 * NOTE: This class REQUIRES MongoDb PECL extension
	 * 
	 * @author Guillaume Bodi <bodi.giyomu@gmail.com>
	 */
	class Mongo 
	{
		const OPT_TYPE = 'type';
		
		const OPT_HOST = 'host';
		
		const OPT_PORT = 'port';
		
		const OPT_DB = 'database';
		
		const OPT_USERNAME = 'username';
		
		const OPT_PASSWORD = 'password';
		
		/*
		 ****************************************************
		 */
		
		const MONGO_CONNECTION_DB = 'db';

		const MONGO_CONNECTION_USERNAME = 'username';

		const MONGO_CONNECTION_PASSWORD = 'password';
		
		const DEFAULT_HOST = 'localhost';

		const DEFAULT_PORT = 27017;
		
		const LOOPBACK_IP = '127.0.0.1';
		
		const DEFAULT_PASSWORD = '';

		protected $dbSchema = '';

		protected $connection = null;

		public function __construct($options)
		{
			$this->prepareConstructorOptions($options);

			try
			{
				$connectionOptions = array();

				if(isset($options[self::OPT_DB])) 
				{
					$this->dbSchema = $options[self::OPT_DB];
					$connectionOptions[self::MONGO_CONNECTION_DB] = $this->dbSchema;
				}
				
				if(isset($options[self::OPT_USERNAME]))
				{
					$connectionOptions[self::MONGO_CONNECTION_USERNAME] = $options[self::OPT_USERNAME];
					$connectionOptions[self::MONGO_CONNECTION_PASSWORD] = $options[self::OPT_PASSWORD];
				} 

				$this->connection = new MongoClient(
					$this->getMongoConnectionString($options),
					$connectionOptions
				);
			}
			catch(MongoException $exception)
			{
				throw new DbException($exception->getMessage(), $exception->getCode(), $options);
			}
		}

		protected function getMongoConnectionString($options)
		{
			return 
				'mongodb://' . $options[self::OPT_HOST] . 
				(
					isset($options[self::OPT_PORT]) && is_numeric($options[self::OPT_PORT]) ?
					':' . $options[self::OPT_PORT] :
					''
				);
		}

		/**
		 * Parses the constructor options and initialise values where missing
		 * @param array $options
		 */
		protected function prepareConstructorOptions(&$options) 
		{
			if(!isset($options[self::OPT_HOST])) 
			{
				$options[self::OPT_HOST] = self::LOOPBACK_IP;
			} 
			else if($options[self::OPT_HOST] == self::DEFAULT_HOST) 
			{
				$options[self::OPT_HOST] = self::LOOPBACK_IP;
			}
			
			if(isset($options[self::OPT_USERNAME]) && !isset($options[self::OPT_PASSWORD])) 
			{
				$options[self::OPT_PASSWORD] = self::DEFAULT_PASSWORD;
			}
		}
	}
}