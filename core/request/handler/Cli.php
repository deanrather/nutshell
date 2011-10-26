<?php
namespace nutshell\core\request\handler
{
	use nutshell\core\request\Handler;
	
	class Cli extends Handler
	{
		public function __construct() {
			$this->parse($_SERVER['argv']);
			parent::__construct();
		}
		
		protected function setupNodes()
		{
			//handled by the parse method
		}
		
		protected function setupData()
		{
			//handled by the parse method
		}
		
		private function parse($arguments) 
		{
			$this->data = array();
			$this->nodes  = array();
			
			array_shift($arguments);
			
			for($i = 0; $i < count($arguments); $i++)
			{
				$str = $arguments[$i];
				
				// --foo
				if(strlen($str) > 2 && substr($str, 0, 2) == '--')
				{
					$str = substr($str, 2);
					$parts = explode('=', $str);
					$this->data[$parts[0]] = true;
					
					// Does not have an =, so choose the next arg as its value
					if(count($parts) == 1 && isset($arguments[$i + 1]) && preg_match('/^--?.+/', $arguments[$i + 1]) == 0)
					{
						$this->data[$parts[0]] = $arguments[$i + 1];
					}
					elseif(count($parts) == 2) // Has a =, so pick the second piece
					{
						$this->data[$parts[0]] = $parts[1];
					}
				}
				elseif(strlen($str) == 2 && $str[0] == '-') // -a
				{
					$this->data[$str[1]] = true;
					if(isset($arguments[$i + 1]) && preg_match('/^--?.+/', $arguments[$i + 1]) == 0) 
					{
						$this->data[$str[1]] = $arguments[$i + 1];
					}
				}
				elseif(strlen($str) > 1 && $str[0] == '-') // -abcdef
				{
					for($j = 1; $j < strlen($str); $j++) 
					{
						$this->data[$str[$j]] = true;
					}
				}
			}
			
			for($i = count($arguments) - 1; $i >= 0; $i--)
			{
				if(preg_match('/^--?.+/', $arguments[$i]) == 0) 
				{
					$this->nodes[] = $arguments[$i];
				}
				else 
				{
					//detect if we need to remove the previous node
					if(preg_match('/^--?.=+/', $arguments[$i]) == 0) {
						@array_pop($this->nodes);
					}
					break;
				}
			}
			
			$this->nodes = array_reverse($this->nodes);
		}
	}
}
?>