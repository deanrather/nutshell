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
		private $compiled		=null;
		
		public static function loadDependencies(){}
		
		public function init($template)
		{
			$this->setTemplate($template);
		}
		
		public function setTemplate($template)
		{
			if (is_file($template))
			{
				if (is_readable($template))
				{
					$this->templateFile	=$template;
					$this->template		=file_get_contents($template);
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
					$this->keyVals['{$'.$key[$i].'}']=$val[$i];
				}
			}
			else
			{
				throw new Exception('Invalid keyval lengths. Keys and values should contain the same number of items.');
			}
			return $this;
		}
		
		public function compile()
		{
			return $this->compiled=str_replace
			(
				array_keys($this->keyVals),
				array_values($this->keyVals),
				$this->template
			);
		}
		
		public function getCompiled()
		{
			if (is_null($this->compiled))
			{
				$this->compile();
			}
			return $this->compiled;
		}
	}
}
?>