<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\formParser\element\field
{
	use nutshell\plugin\formParser\element\Element;
	use \stdClass;
	
	/**
	 * @package nutshell-plugin
	 * @author guillaume
	 */
	class Field extends Element
	{
		private $ref			='';
		private $label			='';
		private $value			='';
		private $helper			='';
		private $class			='';
		private $style			='';
		private $labelPosition	='left';
		
		public function init($elementDef)
		{
			if (isset($elementDef->ref))
			{
				$this->setRef($elementDef->ref);
			}
			if (isset($elementDef->label))
			{
				$this->setLabel($elementDef->label);
			}
			if (isset($elementDef->value))
			{
				$this->setValue($elementDef->value);
			}
			if (isset($elementDef->helper))
			{
				$this->setHelper($elementDef->helper);
			}
			if (isset($elementDef->class))
			{
				$this->setClass($elementDef->class);
			}
			if (isset($elementDef->style))
			{
				$this->setStyle($elementDef->style);
			}
			if (isset($elementDef->labelPosition))
			{
				$this->setLabelPosition($elementDef->labelPosition);
			}
		}
		
		public function setRef($ref)
		{
			$this->ref=$ref;
			return $this;
		}
		
		public function getRef()
		{
			return $this->ref;
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
		
		public function setValue($value)
		{
			$this->value=$value;
			return $this;
		}
		
		public function getValue()
		{
			return $this->value;
		}
		
		public function setHelper($helper)
		{
			$this->helper=$helper;
			return $this;
		}
		
		public function getHelper()
		{
			return $this->helper;
		}
		
		public function setMultiline($multiline)
		{
			$this->multiline=(bool)$multiline;
			return $this;
		}
		
		public function isMultiline()
		{
			return $this->multiline;
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
		
		public function setLabelPosition($labelPosition)
		{
			$this->labelPosition=$labelPosition;
			return $this;
		}
		
		public function getLabelPosition()
		{
			return $this->labelPosition;
		}
		
		public function render()
		{
			$this	->setTemplateVar('REF',				$this->ref)
					->setTemplateVar('LABEL',			$this->label)
					->setTemplateVar('VALUE',			$this->value)
					->setTemplateVar('HELPER',			$this->helper)
					->setTemplateVar('CLASS',			$this->class)
					->setTemplateVar('STYLE',			$this->style)
					->setTemplateVar('LABELPOSITION',	$this->labelPosition);
			return parent::render();
		}
	}
}
?>