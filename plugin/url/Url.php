<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\url
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class Url extends Plugin implements Native,Singleton
	{
		private $nodes=array();
		
		public static function loadDependencies()
		{
			
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			
		}
		
		public function node($num)
		{
			return $this->request->node($num);
		}
		
		public function nodeEmpty($num)
		{
			return $this->request->nodeEmpty($num);
		}
		
		/**
		 * @deprecated
		 */
		public function getNodes()
		{
			return $this->request->getNodes();
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
			$return=$protocol.$_SERVER['HTTP_HOST'].'/'.$URL;
			return $return;
		}
		
		public function getCurrentURL()
		{
			return $this->makeURL().implode('/',$this->request->getNodes());
		}
	}
}
?>