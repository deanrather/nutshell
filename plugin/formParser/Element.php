<?php
namespace nutshell\plugin\formParser
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
		private $children		=null;
		private $templateVars	=array();
		
		public function __construct(stdClass $elementDef)
		{
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
			$template=$this->plugin->Template(Object::getClassPath(get_class($this)).self::TEMPLATE_DIR._DS_.'Page.tpl');
			$template->setKeyVal
			(
				array_keys($this->templateVars),
				array_values($this->templateVars)
			);
			
			return $template->compile();
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
		
		public function addChild()
		{
			
		}
		
		public function getChildren()
		{
			return $this->children;
		}
		
		public function render()
		{
			$this->setTemplateVar('ID',$this->id);
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