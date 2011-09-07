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
	class Radio extends Field
	{
		private $checked=false;
		
		public function init($elementDef)
		{
			parent::init($elementDef);
			if (isset($elementDef->checked))
			{
				$this->setChecked($elementDef->checked);
			}
		}
		
		public function setChecked($checked)
		{
			$this->checked=(bool)$checked?'checked':'';
			return $this;
		}
		
		public function isChecked()
		{
			return (bool)$this->checked;
		}
		
		public function render()
		{
			$this->setTemplateVar('CHECKED',$this->checked);
//			return parent::render();
			if ($this->parentIsType('InputGroup'))
			{
				$this->setTemplateName('GroupedRadio');
			}
			return parent::render();
		}
	}
}
?>