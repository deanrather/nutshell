<?php
namespace nutshell\plugin\mvc\model
{
	use nutshell\Nutshell;
	use nutshell\plugin\mvc\Model;
	use nutshell\core\exception\Exception;
	
	abstract class CRUD extends Model
	{
		public $name	=null;
		public $primary	=null;
		public $columns	=array();
		
		private $types	=array();
		private $db		=null;
		
		public function __construct()
		{
			parent::__construct();
			if (!empty($this->name) && !empty($this->primary) && !empty($this->columns))
			{
//				print Nutshell::getInstance()->config->prettyPrint();
				if ($connection=Nutshell::getInstance()->config->plugin->Mvc->connection)
				{
					$columns=array();
					foreach ($this->columns as $name=>$typeDef)
					{
						$type=array();
						preg_match('/^(\w+)\((\d+)\)?$/',$typeDef,$type);
						$this->types[$type[1]]	=$type[2];
						if ($name!=$this->primary)
						{
							$columns[]			=$name.' '.$typeDef.' NOT NULL AUTO_INCREMENT';
						}
						else
						{
							$columns[]			=$name.' '.$typeDef.' NULL';
						}
					}
					$columns=implode(',',$columns);
					//Make a shortcut reference to the 
					$this->db=Nutshell::getInstance()->plugin->Db->{$connection};
					//Create the table (if it doesn\'t already exist).
					$query=<<<SQL
					CREATE TABLE IF NOT EXISTS {$this->name}
					(
						{$columns},
						PRIMARY KEY ({$this->primary})
					) ENGINE=INNODB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin;
SQL;
					var_dump($query);exit();
					$this->db->query($query);
				}
				else
				{
					throw new Exception('DB connection not defined in config.');
				}
			}
			else
			{
				throw new Exception('CRUD Model is misconfigured. Name, Primary Key and Columns must be defined.');
			}
		}
		
		public function create(Array $record)
		{
			$placeholders	=rtrim(str_repeat('?,',count($this->columns),','));
			$keys			=implode(',',array_keys($record));
			$values			=array_values($record);
			$query=<<<SQL
			INSERT INTO {$this->name}
				({$keys})
			VALUES
				({$placeholders});
SQL;
			return $this->db->insert($query,$values);
		}
		
		/**
		 * @todo - Finish read method.
		 * Enter description here ...
		 * @param unknown_type $whereKeyVals
		 * @param unknown_type $limit
		 * @param unknown_type $order
		 */
		public function read($whereKeyVals=1,$limit=-1,$order='ASC')
		{
			$query=<<<SQL
			SELECT *
			FROM {$this->table}
			
SQL;
		}
		
		public function update($updateKeyVals,$whereKeyVals)
		{
			//Create the set portion of the query.
			$set=array();
			foreach (array_keys($updateKeyVals) as $key)
			{
				$set[]=$key.'=?';
			}
			$set=implode(',',$set);
			//Quick update is possible if $whereKeyVals is numeric.
			if (is_numeric($whereKeyVals))
			{
				$query=<<<SQL
				UPDATE {$this->table}
				SET {$set}
				WHERE {$this->primary}=?;
SQL;
				$params		=array_values($updateKeyVals);
				$params[]	=$whereKeyVals;
				return $this->db->update($query,$params);
			}
			//More specific keyval matching.
			else if (is_array($whereKeyVals))
			{
				$where=array();
				foreach (array_keys($whereKeyVals) as $key)
				{
					$where[]=$key.'=?';
				}
				$where=implode(',',$where);
				$query=<<<SQL
				UPDATE {$this->table}
				SET {$set}
				WHERE {$where};
SQL;
				return $this->db->update($query,array_merge(array_values($updateKeyVals),array_values($whereKeyVals)));
			}
			else
			{
				throw new Exception('$whereKeyVals is invalid. Specify an array of key value pairs or a single numeric for primay key match.');
			}
		}
		
		public function delete($whereKeyVals)
		{
			//Quick delete is possible if $whereKeyVals is numeric.
			if (is_numeric($whereKeyVals))
			{
				$query=<<<SQL
				DELETE FROM {$this->table}
				WHERE {$this->primary}=?;
SQL;
				return $this->db->delete($query,array($whereKeyVals));
			}
			//More specific keyval matching.
			else if (is_array($whereKeyVals))
			{
				$where=array();
				foreach (array_keys($whereKeyVals) as $key)
				{
					$where[]=$key.'=?';
				}
				$where=implode(',',$where);
				$query=<<<SQL
				DELETE FROM {$this->table}
				WHERE {$where};
SQL;
				return $this->db->delete($query,array_values($whereKeyVals));
			}
			else
			{
				throw new Exception('$whereKeyVals is invalid. Specify an array of key value pairs or a single numeric for primay key match.');
			}
		}
	}
}