<?php
namespace nutshell\request
{
	use nutshell\core\Component;
	use nutshell\core\exception\NutshellException;
	
	class Request extends Component
	{
		public function __construct()
		{
			
			
			parent::__construct();
		}
		
		
		public function &get($key)
		{
			return $_REQUEST[$key];
		}
		
		/**
		 * 
		 * @access public
		 * @return Request $this
		 * @throws NutshellException
		 */
		public function set()
		{
			if ($numArgs=func_num_args())
			{
				$args=func_get_args();
				if (!is_array($args[0]))
				{
					$args[0]=array($args[0]=>$args[1]);
				}
				foreach ($args[0] as $key=>$val)
				{
					$_REQUEST[$key]=$val;
				}
			}
			else
			{
				throw new NutshellException('Invalid request set. No args given.');
			}
			return $this;
		}
	}
}
?>