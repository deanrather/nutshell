<?php
namespace nutshell\plugin\FormParser\element\container
{
	use nutshell\plugin\FormParser\Element;
	
	class Page extends Element
	{
		private $title		='';
		private $css		=null;
		private $js			=null;
		
		public function init()
		{
			
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
			
		}
	}
}
?>