<?php
/**
 * @package nutshell-plugin
 * @author Timothy Chandler
 */
namespace nutshell\plugin\formParser\element\button
{
	use nutshell\plugin\formParser\element\Element;
	use \stdClass;
	
	/**
	 * @package nutshell-plugin
	 * @author Timothy Chandler
	 */
	class Button extends Element
	{
		private $type			='';
		private $label			='';
		private $class			='';
		private $style			='';
		
		public function init($elementDef)
		{
			if (isset($elementDef->type))
			{
				$this->setType($elementDef->type);
			}
			if (isset($elementDef->label))
			{
				$this->setLabel($elementDef->label);
			}
			if (isset($elementDef->class))
			{
				$this->setClass($elementDef->class);
			}
			if (isset($elementDef->style))
			{
				$this->setStyle($elementDef->style);
			}
		}
		
		public function setType($type)
		{
			$this->type=$type;
			return $this;
		}
		
		public function getType()
		{
			return $this->type;
		}
		
		public function setLabel($label)
		{
			$this->label=$label;
			return $this;
		}
		
		public function getLabel()
		{
			return $this->label;
		}
		
		public function setClass($class)
		{
			$this->class=$class;
			return $this;
		}
		
		public function getClass()
		{
			return $this->class;
		}
		
		public function setStyle($style)
		{
			if ($style instanceof stdClass)
			{
				$this->style='';
				foreach ($style as $key=>$val)
				{
					$this->style.=$key.':'.$val.';';
				}
			}
			else
			{
				$this->style=$style;
			}
			return $this;
		}
		
		public function getStyle()
		{
			return $this->style;
		}
		
		public function render()
		{
			$this	->setTemplateVar('TYPE',			$this->type)
					->setTemplateVar('LABEL',			$this->label)
					->setTemplateVar('CLASS',			$this->class)
					->setTemplateVar('STYLE',			$this->style);
			return parent::render();
		}
	}
}
?>