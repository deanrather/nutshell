<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\formParser\element
{
	use nutshell\core\plugin\PluginExtension;
	use nutshell\helper\Object;
	use nutshell\helper\String;
	use \stdClass;
	
	/**
	 * @package nutshell-plugin
	 * @author guillaume
	 * @abstract
	 */
	abstract class Element extends PluginExtension
	{
		abstract public function init($elementDef);
		
		const TEMPLATE_DIR		='tpl';
		
		private $parent			=null;
		private $id				=null;
		private $children		=array();
		private $templateVars	=array();
		private $templateName	=null;
		
		public function __construct($parent,stdClass $elementDef)
		{
			$this->parent=$parent;
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
		
		public function getParent()
		{
			return $this->parent;
		}
		
		private function compileTemplate()
		{
			$elementTpl=self::getTemplateFile($this);
			
			if ($elementTpl)
			{
				//Now handle any visual inheritence.
				$thisClass		=get_class($this);
				$parentClass	=get_parent_class($thisClass);
				$parentName		=Object::getBaseClassName($parentClass);
				
//				var_dump(Object::getBaseClassName($thisClass));
//				var_dump($parentName);
				//If the parent class is not Element, then fetch the template for that class.
				if ($parentName!='Element')
				{
					$templateDir	=Object::getClassPath($parentClass).self::TEMPLATE_DIR._DS_;
					$file			=$templateDir.Object::getBaseClassName($parentName).'.tpl';
					$parentTpl		=$parentClass::getTemplateFile($this);
					
//					var_dump($elementTpl);
//					var_dump($parentTpl);
					if (is_null($this->getTemplateName()))
					{
						$limit=5;	//In case of any major developer screw-ups :)
						while ($elementTpl==$parentTpl && --$limit)
						{
							$parentClass	=get_parent_class($parentClass);
							$parentName		=Object::getBaseClassName($parentClass);
							$parentTpl		=$parentClass::getTemplateFile($this);
//							var_dump($parentName);
						}
					}
					
//					var_dump($elementTpl);
//					var_dump($parentTpl);
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
		
		public static function getTemplateFile($scope)
		{
			$class	=get_called_class();
			$file	=null;
			if (is_null($scope->getTemplateName()))
			{
				$file=Object::getClassPath($class).self::TEMPLATE_DIR._DS_.Object::getBaseClassName($class).'.tpl';
			}
			else
			{
				$file=Object::getClassPath($class).self::TEMPLATE_DIR._DS_.$scope->getTemplateName().'.tpl';
			}
//			var_dump(Object::getBaseClassName($class));
//			var_dump($file);
			$limit=5;	//In case of any major developer screw-ups :)
			while (!file_exists($file) && --$limit)
			{
//				var_dump($limit);
				$class	=get_parent_class($class);
				$file	=Object::getClassPath($class).self::TEMPLATE_DIR._DS_.Object::getBaseClassName($class).'.tpl';
//				var_dump(Object::getBaseClassName($class));
//				var_dump($file);
//				if (Object::getBaseClassName($class)!='Element')
//				{
//					$class=get_parent_class($class);
//				}
//				else
//				{
//					return '!~!INVALID ELEMENT!~!!~!MISSING FALLBACK TEMPLATE!~!';
//					break;
//				}
			}
			return $file;
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
		
		/**
		 * Sets the name of the template to use.
		 * 
		 * !!IMPORTANT: Using this will force the renderer to NOT use
		 * any visual inheritance.
		 * 
		 * @access public
		 * @param String $templateName - The name of the template to use.
		 * @return nutshell\plugin\formParser\element\Element
		 */
		public function setTemplateName($templateName)
		{
			$this->templateName=$templateName;
			return $this;
		}
		
		public function getTemplateName()
		{
			return $this->templateName;
		}
		
		public function parentIsType($type,$strict=false)
		{
			if (!$strict)
			{
				$current=$this->parent;
				while (Object::getBaseClassName($current)!='Page')
				{
					if (Object::getBaseClassName($current)==$type)
					{
						return true;
					}
					//Failsafe
					if (!isset($current->parent))break;
					$current=$current->parent;
				}
			}
			else
			{
				return (Object::getBaseClassName($this->parent)==$type);
			}
			return false;
		}
	}
}
?>