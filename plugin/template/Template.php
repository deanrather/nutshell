<?php
namespace nutshell\plugin\template
{
	use nutshell\core\exception\Exception;
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Factory;
	
	class Template extends Plugin implements Native,Factory
	{
		private $templateFile	=null;
		private $template		=null;
		private $keyVals		=array();
		private $context		=null;
		private $compiled		=null;
		
		public static function loadDependencies()
		{
			require('Context.php');
		}
		
		public function init($template)
		{
			$this->setTemplate($template);
			$this->context=new Context($this->keyVals);
		}
		
		public function setTemplate($template)
		{
			if (is_file($template))
			{
				if (is_readable($template))
				{
					$this->templateFile=$template;
				}
				else
				{
					throw new Exception('Unable to load template. File is unreadable. FILE: "'.$template.'".');
				}
			}
			else
			{
				throw new Exception('Unable to load template. File is missing. FILE: "'.$template.'".');
			}
		}
		
		public function setKeyVal($key,$val)
		{
			if (!is_array($key))	$key=array($key);
			if (!is_array($val))	$val=array($val);
			if (count($key)==count($val))
			{
				for ($i=0,$j=count($key); $i<$j; $i++)
				{
					$this->keyVals[$key[$i]]=$val[$i];
				}
			}
			else
			{
				throw new Exception('Invalid keyval lengths. Keys and values should contain the same number of items.');
			}
			return $this;
		}
		
		public function compile($clear=true)
		{
			$tpl=$this->context;
			$closedScopeClosure=function($templateFile) use (&$tpl)
			{
				ob_start();
				include($templateFile);
				$compiled=ob_get_contents();
				ob_end_clean();
				return $compiled;
			};
			$this->compiled=$closedScopeClosure($this->templateFile);
			return $this->compiled;
		}
		
		public function getCompiled()
		{
			if (is_null($this->compiled))
			{
				$this->compile();
			}
			return $this->compiled;
		}
		
		public function setContext(Context $context)
		{
			$this->context=$context;
			return $this;
		}
		
		public function getContext()
		{
			return $this->context;
		}
	}
}
?>