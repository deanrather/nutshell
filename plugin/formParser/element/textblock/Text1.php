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
	class Text1 extends Textblock
	{
		public function init($elementDef)
		{
			$this->setClass('text1');
			parent::init($elementDef);
		}
	}
}
?>