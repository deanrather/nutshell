<?php
namespace nutshell\plugin\formParser\element\container
{
	use nutshell\plugin\formParser\Element;
	
	class Page extends Element
	{
		private $title		='';
		private $css		=null;
		private $js			=null;
		
		public static function preload()
		{
			self::depends('parent','Element');
		}
		
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