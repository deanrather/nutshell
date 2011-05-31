<?php
namespace nutshell\plugin\formParser\element
{
	use nutshell\core\plugin\PluginExtension;
	use nutshell\helper\Object;
	use nutshell\helper\String;
	use \stdClass;
	
	abstract class Element extends PluginExtension
	{
		abstract public function init($elementDef);
		
		const TEMPLATE_DIR		='tpl';
		
		private $id				=null;
		private $children		=array();
		private $templateVars	=array();
		
		public function __construct(stdClass $elementDef)
		{
			parent::__construct();
			if (isset($elementDef->id))
			{
				$this->id=$elementDef->id;
			}
			else
			{
				$this->generateID();
			}
			$this->init($elementDef);
		}
		
		public function __toString()
		{
			return (string)$this->render();
		}
		
		private function compileTemplate()
		{
			
			$templateFile=$this->getTemplateFile();
			if ($templateFile)
			{
				$template=$this->plugin->Template();
				$template->setKeyVal
				(
					array_keys($this->templateVars),
					array_values($this->templateVars)
				);
			}
			return $template->compile();
		}
		
		public function getTemplateFile()
		{
			$file=Object::getClassPath($this).self::TEMPLATE_DIR._DS_.Object::getBaseClassName($this).'.tpl';
			if (file_exists($file))
			{
				return ;
			}
			else
			{
				$parent=get_parent_class($this);
				if (method_exists($parent,'getTemplateFile'))
				{
					
				}
				return $parent::getTemplateFile();
			}
		}
		
		private function generateID()
		{
			$this->id='fb-'.String::getRandomString();
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
		
		public function addChild(Element $child)
		{
			$this->children[]=$child;
			return $this;
		}
		
		public function getChildren()
		{
			return $this->children;
		}
		
		public function render()
		{
			$children='';
			for ($i=0,$j=count($this->children); $i<$j; $i++)
			{
				$children.=$this->children[$i]->render();
			}
			$this->setTemplateVar('ID',$this->id);
			$this->setTemplateVar('CHILDREN',$children);
			return $this->compileTemplate();
		}
		
		public function setTemplateVar($key,$val)
		{
			$this->templateVars[$key]=$val;
			return $this;
		}
	}
}
?>