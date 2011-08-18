<?php
namespace application\model
{
	use nutshell\plugin\mvc\model\CRUD;
	
	class Account extends CRUD
	{
		public $name	='account';
		public $primary	='id';
		
		public $columns=array
		(
			'id'		=>'int(10)',
			'auth_id'	=>'varchar(36)',
			'state'		=>'text',
			'status'	=>'tinyint(1)'
		);
	}
}
?>