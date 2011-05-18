<?php
namespace nutshell\plugin\formParser
{
	class Document
	{
		private $elements	=array();
		private $lastRender	=null;
		
		public function __construct()
		{
			
		}
		
		public function render()
		{
			for ($i=0,$j=count($this->elements); $i<$j; $i++)
			{
				$this->elements[$i]->render();
			}
		}
	}
}
?>