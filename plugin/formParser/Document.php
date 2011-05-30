<?php
namespace nutshell\plugin\formParser
{
	use nutshell\plugin\formParser\element\container\Page;

	class Document
	{
		private $elements	=array();
		private $root		=null;
		private $lastRender	=null;
		
		public function __construct(Array $json)
		{
			$this->parse($json);
		}
		
		public function __toString()
		{
			return (string)$this->render();
		}
		
		private function parse(Array $json)
		{
			//Create the root document element.
			$this->root=$this->elements[]=new Page($json[0]);
			
//			for ($i=0,$j=count($json); $i<$j; $i++)
//			{
//				$class=$this->getClassFromType($json[$i]['type']);
//			}
		}
		
		public function render()
		{
			return $this->root->render();
		}
		
		private function getClassFromType($type)
		{
			$parts=explode('.',$type);
			switch ($parts[0])
			{
				case '':
			}
		}
	}
}
?>