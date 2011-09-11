<?php
/**
 * @package nutshell-plugin
 * @author Timothy Chandler
 */
namespace nutshell\plugin\formParser\element\textblock
{
	use nutshell\plugin\formParser\element\Element;
	
	/**
	 * @package nutshell-plugin
	 * @author Timothy Chandler
	 */
	class Textblock extends Element
	{
		private $text	='';
		private $class	='';
		private $style	='';
		
		public function init($elementDef)
		{
			if (isset($elementDef->text))
			{
				$this->setText($elementDef->text);
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
		
		public function setText($text)
		{
			$this->text=$text;
			return $this;
		}
		
		public function getText()
		{
			return $this->text;
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
			$this	->setTemplateVar('TEXT',			$this->text)
					->setTemplateVar('CLASS',			$this->class)
					->setTemplateVar('STYLE',			$this->style);
			return parent::render();
		}
	}
}
?>