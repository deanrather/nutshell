<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\router\handler
{
	use nutshell\core\plugin\PluginExtension;
	use nutshell\plugin\router\Route;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Simple extends PluginExtension
	{
		private $route		=null;
		private $pointer	=0;
		
		public function __construct()
		{
			$this->calculateRoute();
		}
		
		private function calculateRoute()
		{
			if ($this->plugin->Url->nodeEmpty($this->pointer))
			{
				$this->route=new Route('index','index',array());
			}
			else
			{
				//Set the controler to node 0.
				$control=$this->plugin->Url->node($this->pointer);
			
				//Set some defaults.
				$action	='index';
				$args	=array();
			
				//Check for action.
				$node=$this->plugin->Url->node($this->pointer+1);
				if (!is_null($node))
				{
					//Set action to node 1.
					$action	=$node;
					$node	=$this->pointer+2;
					//Check for args.
					if (!is_null($node))
					{
						while (true)
						{
							//grab the next node
							$arg = $this->plugin->Url->node($node++);
							
							if(is_null($arg))
							{
								break;
							}
							//append to the args array
							$args[] = $arg;
						}
					}
				}
				if (is_null($this->route))
				{
					$this->route=new Route($control,$action,$args);
				}
				else
				{
					$this->route->setControl($control)
								->setAction($action)
								->setArgs($args);
				}
			}
		}
		
		public function advancePointer()
		{
			$this->pointer++;
			$this->calculateRoute();
		}
		
		public function rewindPointer()
		{
			$this->pointer--;
			$this->calculateRoute();
		}
		
		public function getRoute()
		{
			return $this->route;
		}
	}
}
?>