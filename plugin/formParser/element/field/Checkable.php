<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\formParser\element\field
{
	/**
	 * @package nutshell-plugin
	 * @author guillaume
	 */
	class Checkable extends Field
	{
		public $type	='';
		private $checked='';
		
		public function init($elementDef)
		{
			parent::init($elementDef);
			if (isset($elementDef->checked))
			{
				$this->setChecked($elementDef->checked);
			}
			if (!isset($elementDef->value))
			{
				$this->setValue($elementDef->label);
			}
		}
		
		public function getType()
		{
			return $this->type;
		}
		
		public function setChecked($checked)
		{
			$this->checked=(bool)$checked?'checked="checked"':'checked=""';
			return $this;
		}
		
		public function isChecked()
		{
			return (bool)$this->checked;
		}
		
		public function render()
		{
			$this->setTemplateVar('TYPE',$this->type);
			$this->setTemplateVar('CHECKED',$this->checked);
			if ($this->parentIsType('InputGroup'))
			{
				$this->setTemplateVar('GROUPED',true);
				$this->setTemplateName('Checkable');
			}
			return parent::render();
		}
	}
}
?>