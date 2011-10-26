<?php
namespace nutshell\core\request\handler
{
	use nutshell\core\request\Handler;
	
	class Http extends Handler
	{
		public function __construct()
		{
			$this->data=&$_REQUEST;
			parent::__construct();
		}
	}
}
?>