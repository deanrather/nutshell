<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\formParser
{
	use nutshell\core\exception\Exception;
	use nutshell\plugin\formParser\element\container\Page;

	/**
	 * @package nutshell-plugin
	 * @author guillaume
	 */
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
			$this->root=new Page($this,$json[0]);
			//Now creat it's children.
			if (isset($json[0]->children))
			{
				$this->parseChildren($this->root,$json[0]->children);
			}
		}
		
		private function parseChildren($parent,$children)
		{
			for ($i=0,$j=count($children); $i<$j; $i++)
			{
				if (!is_null($class=$this->getClassFromType($children[$i]->type)))
				{
					$element=new $class($parent,$children[$i]);
					if (isset($children[$i]->children))
					{
						$this->parseChildren($element,$children[$i]->children);
					}
					$parent->addChild($element);
				}
				else
				{
					throw new Exception('Unsupported element type "'.$children[$i]->type.'".');
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
						case 'page':		$class.='Page';			break;
						case 'group':		$class.='Group';		break;
//						case 'inputgroup':	$class.='InputGroup';	break;
						case 'radiogroup':	$class.='RadioGroup';	break;
						default:			$class=null;
					}
					break;
				}
				case 'field':
				{
					$class=__NAMESPACE__.'\element\field\\';
					switch ($parts[1])
					{
						case 'text':	$class.='Text';		break;
						case 'radio':	$class.='Radio';	break;
						default:		$class=null;
					}
					break;
				}
			}
			return $class;
		}
	}
}
?>