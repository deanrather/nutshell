<?php
namespace nutshell\plugin\formParser
{
	use nutshell\core\Plugin;
	use nutshell\core\exception\Exception;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Factory;
	
	class FormParser extends Plugin implements Native,Factory
	{
		private $path	=null;
		private $raw	=null;
		private $json	=null;
		private $parsed	=null;
		
		public function init($document=null)
		{
			include_once(__DIR__.'/Document.php');
			include_once(__DIR__.'/Element.php');
			include_once(__DIR__.'/element/container/Page.php');
			include_once(__DIR__.'/element/container/Group.php');
			include_once(__DIR__.'/element/field/Text.php');
			include_once(__DIR__.'/element/rule/Required.php');
			
			if (is_string($document))
			{
				$this->loadDocument($document);
			}
		}
		
		public function loadDocument($document)
		{
			$this->path	=$document;
			if (is_file($this->path))
			{
				$this->raw	=file_get_contents($this->path);
				$this->json	=json_decode($this->raw);
			}
			else
			{
				throw new Exception('Unable to load document. The document could not be found.');
			}
			return $this;
		}
		
		public function parse()
		{
			
		}
		
		public function render()
		{
			return 'render';
		}
	}
}
?>