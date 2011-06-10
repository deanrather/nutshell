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
			$elementTpl=self::getTemplateFile();
			if ($elementTpl)
			{
				//Now handle any visual inheritence.
				$thisClass		=get_class($this);
				$parentClass	=get_parent_class($thisClass);
				$parentName		=Object::getBaseClassName($parentClass);
				
				//If the parent class is not Element, then fetch the template for that class.
				if ($parentName!='Element')
				{
					$templateDir	=Object::getClassPath($parentClass).self::TEMPLATE_DIR._DS_;
					$file			=$templateDir.Object::getBaseClassName($parentName).'.tpl';
					$parentTpl		=$parentClass::getTemplateFile();
					
					//This will place the child template inside of the parent template.
					$elementTpl		=$this->plugin->Template($elementTpl);
					$template		=$this->plugin->Template($parentTpl);
					
					//Bind the variables for the element template.
					$elementTpl->setKeyVal
					(
						array_keys($this->templateVars),
						array_values($this->templateVars)
					);
					/* Now compile the element template and push it into
					 * the variable list so the parent template can compile it into itself.
					 */
					$this->setTemplateVar('EXTENDED',$elementTpl->compile());
					$template->setKeyVal
					(
						array_keys($this->templateVars),
						array_values($this->templateVars)
					);
					return $template->compile();
				}
				else
				{
					$template=$this->plugin->Template($elementTpl);
					$template->setKeyVal
					(
						array_keys($this->templateVars),
						array_values($this->templateVars)
					);
					return $template->compile();
				}
			}
			return '';
		}
		
		public static function getTemplateFile()
		{
			$class	=get_called_class();
			$file	=Object::getClassPath($class).self::TEMPLATE_DIR._DS_.Object::getBaseClassName($class).'.tpl';
			if (file_exists($file))
			{
				return $file;
			}
			else
			{
				$parent=get_parent_class($class);
				if (method_exists($parent,'getTemplateFile'))
				{
					return $parent::getTemplateFile();
				}
			}
			return null;
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