<?php
namespace nutshell\plugin\formParser\element\container
{
	use nutshell\plugin\formParser\Element;
	
	class Page extends Element
	{
		private $title		='';
		private $css		='';
		private $js			='';
		
		public function init($elementDef)
		{
			if (isset($elementDef->title))
			{
				$this->setTitle($elementDef->title);
			}
			if (isset($elementDef->css))
			{
				$this->setTitle($elementDef->css);
			}
			if (isset($elementDef->js))
			{
				$this->setTitle($elementDef->js);
			}
		}
		
		public function setTitle($title)
		{
			$this->title=$title;
			return $this;
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		public function setCSS($css)
		{
			$this->css=$css;
			return $this;
		}
		
		public function getCSS()
		{
			return $this->css;
		}
		
		public function setJS($js)
		{
			$this->js=$js;
			return $this;
		}
		
		public function getJS()
		{
			return $this->js;
		}
		
		public function render()
		{
			$this	->setTemplateVar('TITLE',$this->title)
					->setTemplateVar('CSS',$this->css)
					->setTemplateVar('JS',$this->js);
			return parent::render();
		}
	}
}
?>