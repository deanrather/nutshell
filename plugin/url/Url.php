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
			$nodes=explode('/',$_SERVER['REQUEST_URI']);
			if (!reset($nodes))	array_shift($nodes);
			if (!end($nodes))	array_pop($nodes);
			if (!isset($nodes[0]))
			{
				$nodes[0]='';
			}
			if (substr(current($nodes),0,1)=='?')
			{
				array_pop($nodes);
			}
			$this->nodes=$nodes;
		}
		
		public function node($num)
		{
			if (isset($this->nodes[$num]))
			{
				return $this->nodes[$num];
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
		
		public function makeURL($URL='',$protocol='http://')
		{
			$URL=trim($URL,'/');
			if (strstr($URL,'?'))
			{
				list($prefix,$suffix)=explode('?',$URL,2);
				$URL=trim($prefix,'/');
				$URL.=(empty($prefix)?'?':'/?').$suffix;
			}
			return $protocol.$_SERVER['HTTP_HOST'].'/'.$URL.'/';
		}
	}
}
?>