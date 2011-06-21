<?php
namespace nutshell\plugin\mvc
{
	use nutshell\core\plugin\PluginExtension;
	
	class View extends PluginExtension
	{
		private $MVC			=null;
		private $templateVars	=array();
		private $viewFile		=null;
		public $templateContext=null;
		
		public function __construct(Mvc $MVC)
		{
			parent::__construct();
			$this->MVC=$MVC;
		}
		
		public function setTemplate($viewName)
		{
			$this->viewFile=$this->buildViewPath($viewName);
		}
		
		public function buildViewPath($viewName)
		{
			return APP_HOME.$this->MVC->config->dir->views.$viewName.'.php';
		}
		
		public function render()
		{
			$template=$this->plugin->Template($this->viewFile);
			$template->setKeyVal
			(
				array_keys($this->templateVars),
				array_values($this->templateVars)
			);
			//Register some fucntions that can be used in the template.
			$this->templateContext=$template->getContext();
			$scope=$this;
			$this->templateContext->registerCallback
			(
				'loadView',
				function($viewName) use ($scope)
				{
					$template=$scope->plugin->Template($scope->buildViewPath($viewName));
					$template->setContext($scope->templateContext);
					print $template->compile();
				}
			);
			print $template->compile();
		}
		
		public function setVar($key,$value)
		{
			$this->templateVars[$key]=$value;
			return $this;
		}
		
		public function setVars(Array $keyVals)
		{
			foreach ($keyVals as $key=>$val)
			{
				$this->templateVars[$key]=$val;
			}
			return $this;
		}
		
		public function getVar()
		{
			return ($this->templateVars[$key])?$this->templateVars[$key]:null;
		}
		
		public function getContext()
		{
			return $this->templateContext;
		}
	}
}
?>