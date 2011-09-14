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
	class Cli extends PluginExtension
	{
		private $route	=null;
		
		const SHORT_OPTION_MARKER = '-';
		
		const OPTION_CONTROLLER = 'c';
		
		const OPTION_ACTION = 'a';
		
		public function __construct()
		{
			$controller = 'index';
			$action = 'index';
			$args = $_SERVER['argv'];
			//remove the first element: the file name
			array_shift($args);
			 
			$options = getopt(
					self::OPTION_CONTROLLER . ':'
				. 	self::OPTION_ACTION . ':'
			);
			
			if(isset($options[self::OPTION_CONTROLLER])) {
				$controller = $options[self::OPTION_CONTROLLER];
				//clearing the options
				$args = $this->removeFirstParam(
					$args, 
					array(
						self::SHORT_OPTION_MARKER . self::OPTION_CONTROLLER, 
						$options[self::OPTION_CONTROLLER]
					)
				);
			}
			
			if(isset($options[self::OPTION_ACTION])) {
				$action = $options[self::OPTION_ACTION];
				//clearing the options
				$args = $this->removeFirstParam(
					$args, 
					array(
						self::SHORT_OPTION_MARKER . self::OPTION_ACTION, 
						$options[self::OPTION_ACTION]
					)
				);
			}
			
			$this->route=new Route($controller,$action,$args);
		}
		
		protected function removeFirstParam($params, $values) {
			//convert $params into an array
			if(!is_array($values)) {
				$values = array($values);
			}
			foreach($values as $value) {
				foreach($params as $k => $v) {
					if($v == $value) {
						unset($params[$k]);
						break;
					}
				}
			}
			
			return array_filter($params, array($this, 'removeFilter'));
		}
		
		protected function removeFilter($value) {
			return !is_null($value);
		}
		
		public function getRoute()
		{
			return $this->route;
		}
	}
}
?>