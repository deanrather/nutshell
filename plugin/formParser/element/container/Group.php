<?php
namespace nutshell\plugin\formParser\element\container
{
	use nutshell\plugin\formParser\Element;
	
	class Group extends Element
	{
		private $label='';
		
		public function init($elementDef)
		{
			if (isset($elementDef->label))
			{
				$this->setLabel($elementDef->label);
			}
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
		
		public function render()
		{
			$this->setTemplateVar('LABEL',$this->label);
			return parent::render();
		}
	}
}
?>