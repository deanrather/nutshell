<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\mvc
{
	use nutshell\core\exception\Exception;
	use nutshell\Nutshell;

	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 * @abstract
	 */
	abstract class Controller
	{
		public $core	=null;
		public $plugin	=null;
		public $MVC		=null;
		public $view	=null;
		
		public function __construct(Mvc $MVC)
		{
			$this->core		=Nutshell::getInstance();
			$this->plugin	=$this->core->plugin;
			$this->MVC		=$MVC;
			$this->view		=new View($this->MVC);
			$this->core->getLoader()->registerContainer('model',APP_HOME.'model'._DS_,'application\model\\');
		}
		
		public function __get($key)
		{
			if ($key=='model')
			{
				$loader=$this->core->getLoader();
				return $loader('model');
			}
			else
			{
				throw new Exception('Attempted to get invalid property "'.$key.'" from controller.');
			}
		}
	}
}
?>