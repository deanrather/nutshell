<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\router\handler
{
	use nutshell\core\plugin\PluginExtension;
	use nutshell\plugin\router\Route;
	
	/**
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell-plugin
	 */
	class SimpleRest extends PluginExtension
	{
		private $route = null;
		private $pointer = 0;
		
		public function __construct() {
			$this->calculateRoute();
		}
		
		public function calculateRoute()
		{
			if ($this->plugin->Url->nodeEmpty($this->pointer))
			{
				$this->route = new Route('index','index',array());
			}
			else
			{
				//Set the controller to node @pointer.
				$control = $this->plugin->Url->node($this->pointer);
				
				//Set some defaults.
				$action	= 'index';
				$args	= array();
				
				//Check for action.
				if (!is_null($this->plugin->Url->node($this->pointer + 1)))
				{
					//Set args to nodes @pointer + 1 onwards.
					$node = $this->pointer + 1;
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
				$this->route = new Route($control,$action,$args);
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