<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\formParser\element\container
{
	use nutshell\plugin\formParser\element\Element;
	
	/**
	 * @package nutshell-plugin
	 * @author guillaume
	 */
	class Group extends Element
	{
		private $questionNumber	=false;
		private $label			='';
		
		public function init($elementDef)
		{
			if (isset($elementDef->questionNumber))
			{
				$this->setQuestionNumber($elementDef->questionNumber);
			}
			if (isset($elementDef->label))
			{
				$this->setLabel($elementDef->label);
			}
		}
		
		public function setQuestionNumber($questionNumber)
		{
			$this->questionNumber=$questionNumber;
			return $this;
		}
		
		public function getQuestionNumber()
		{
			return $this->questionNumber;
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
			if ($this->questionNumber)
			{
				$this->setTemplateVar('QUESTIONNUMBER',	$this->questionNumber.'.');
			}
			$this->setTemplateVar('LABEL',			$this->label);
			return parent::render();
		}
	}
}
?>