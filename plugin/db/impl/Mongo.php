<?php
namespace nutshell\plugin\db\impl
{
	use \MongoClient;
	use \MongoDB;
	use \MongoCollection;
	use \MongoCursor;
	use \MongoException;

	use nutshell\plugin\db\exception\DbException;

	/**
	 * NOTE: This class REQUIRES MongoDb PECL extension
	 * 
	 * @author Guillaume Bodi <bodi.giyomu@gmail.com>
	 */
	class Mongo 
	{
		const ASCENDING = MongoCollection::ASCENDING;

		const DESCENDING = MongoCollection::DESCENDING;

		const HANDLER_MONGO = 'mongo';

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

		protected $databaseHandle = null;

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

				$this->databaseHandle = $this->connection->selectDB($this->dbSchema);
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
		 * Ignored
		 */
		public function setCharset($charset) {
			// mongo uses UTF-8
		}

		/**
		 * Checks whether the DB manager has secured a valid DB connection or not.
		 * 
		 * @return boolean true if a connection to the server could be established, false otherwise
		 */
		public function isConnected() {
			return !is_null($this->connection);
		}

		/**
		 * Overloader method for dealing with a case where the PDO object is lost on a variable.
		 * 
		 * @access public
		 * @return void
		 */
		public function __destruct()
		{
			$this->disconnect();
		}

		/**
		 * Disconnects the database connection.
		 * 
		 * @access public
		 * @return $this
		 */
		public function disconnect()
		{
			if($this->connection)
			{
				$this->connection->close();
				unset($this->connection);
			}
			return $this;
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

		public function getCollection($collectionName)
		{
			return $this->databaseHandle->selectCollection($collectionName);
		}

		public function find($collection, $query = array(), $fields = array(), $sortFields = array(), $limit = null, $offset = null)
		{
			$resultSet = array();

			try {
				$cursor = $this->getCollection($collection)->find($query, $fields);
				if($sortFields)
				{
					$cursor->sort($sortFields);
				}

				if(is_numeric($limit))
				{
					$cursor->limit($limit);
				}
				if(is_numeric($offset))
				{
					$cursor->skip($offset);
				}

				
				while($cursor->hasNext())
				{
					$resultSet[] = $cursor->getNext();
				}

				return $resultSet;
			}
			catch(MongoException $me)
			{
				throw new DbException($me->getMessage(), $me->getCode(), $me);
			}
		}

		public function insert($collection, $object)
		{
			try {
				$this->getCollection($collection)->insert($object);
			}
			catch(MongoException $me)
			{
				throw new DbException($me->getMessage(), $me);
			}
		}

		public function save($collection, $object)
		{
			try {
				$this->getCollection($collection)->save($object);
			}
			catch(MongoException $me)
			{
				throw new DbException($me->getMessage(), $me);
			}
		}

		public function update($collection, $query = array(), $update = array(), $multiUpdate = false)
		{
			try {
				$this->getCollection($collection)->update($query, $update, array(
					'multiple' => $multiUpdate == true
				));
			}
			catch(MongoException $me)
			{
				throw new DbException($me->getMessage(), $me);
			}
		}

		public function delete($collection, $query, $justOne = false)
		{
			try {
				$this->getCollection($collection)->remove($query, array(
					'justOne' => $justOne == true
				));
			}
			catch(MongoException $me)
			{
				throw new DbException($me->getMessage(), $me);
			}
		}
	}
}