<?php
namespace nutshell\plugin\formParser\element\field
{
	class Text extends Field
	{
		private $multiline	=false;
		
		public function init($elementDef)
		{
			parent::init($elementDef);
			if (isset($elementDef->multiline))
			{
				$this->setMultiline($elementDef->multiline);
			}
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
		
		public function render()
		{
			$this->setTemplateVar('MULTILINE',$this->multiline);
			return parent::render();
		}
	}
}
?>