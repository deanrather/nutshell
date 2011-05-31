<?php
namespace nutshell\plugin\formParser
{
	use nutshell\core\exception\Exception;
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
			$this->root=new Page($json[0]);
			//Now creat it's children.
			for ($i=0,$j=count($json[0]->children); $i<$j; $i++)
			{
				if (!is_null($class=$this->getClassFromType($json[0]->children[$i]->type)))
				{
					$element=new $class($json[0]->children[$i]);
					$this->root->addChild($element);
				}
				else
				{
					throw new Exception('Unsupported element type "'.$json[0]->children[$i]->type.'".');
				}
			}
		}
		
		public function render()
		{
			return $this->root->render();
		}
		
		private function getClassFromType($type)
		{
			$class=null;
			$parts=explode('.',$type);
			switch ($parts[0])
			{
				case 'container':
				{
					$class=__NAMESPACE__.'\element\container\\';
					switch ($parts[1])
					{
						case 'page':	$class.='Page';		break;
						case 'group':	$class.='Group';	break;
					}
					break;
				}
			}
			return $class;
		}
	}
}
?>