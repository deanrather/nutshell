<?php
namespace nutshell\plugin\formParser
{
	abstract class Element
	{
		abstract function render();
		
		
		private $id			=null;
		private $children	=null;
		
		public function __construct()
		{
			
		}
		
		private function generateID()
		{
			
		}
		
		public function setID($id)
		{
			$this->id=$id;
			return $this;
		}
		
		public function getID()
		{
			return $this->id;
		}
		
		public function addChild()
		{
			
		}
		
		public function getChildren()
		{
			return $this->children;
		}
	}
}
?>