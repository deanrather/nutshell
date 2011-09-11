<?php
/**
 * @package nutshell-plugin
 * @author Timothy Chandler
 */
namespace nutshell\plugin\formParser\element\textblock
{
	use nutshell\plugin\formParser\element\textblock\Textblock;
	
	/**
	 * @package nutshell-plugin
	 * @author Timothy Chandler
	 */
	class Text2 extends Textblock
	{
		public function init($elementDef)
		{
			$this->setClass('text2');
			parent::init($elementDef);
		}
	}
}
?>