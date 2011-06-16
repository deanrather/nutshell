<?php
namespace nutshell\plugin\url
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	
	class Url extends Plugin implements Native,Singleton
	{
		private $nodes=array();
		
		public static function loadDependencies()
		{
			
		}
		
		public function init()
		{
			$nodes=explode('/',strtolower($_SERVER['REQUEST_URI']));
			if (!end($nodes))	array_pop($nodes);
			if (!reset($nodes))	array_shift($nodes);
			if (!isset($nodes[0]))
			{
				$nodes[0]='';
			}
			$this->nodes=$nodes;
		}
		
		public function node($num)
		{
			if (isset($this->node[$num]))
			{
				return $this->node[$num];
			}
			return null;
		}
		
		public function nodeEmpty($num)
		{
			return (isset($this->nodes[$num]) && empty($this->nodes[$num]));
		}
		
		public function getNodes()
		{
			return $this->nodes;
		}
	}
}
?>