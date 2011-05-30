<?php
namespace nutshell\plugin\formParser
{
	use nutshell\core\plugin\Plugin;
	use nutshell\core\exception\Exception;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Factory;
	use nutshell\plugin\formParser\Document;
	
	class FormParser extends Plugin implements Native,Factory
	{
		private $path		=null;
		private $raw		=null;
		private $json		=null;
		private $document	=null;
		
		public static function loadDependencies()
		{
//			self::depends('plugin','Db');
//			self::depends('local','Document');
//			self::depends('local','Element');
			
			
			require(__DIR__.'/Document.php');
			require(__DIR__.'/Element.php');
			require(__DIR__.'/element/container/Page.php');
			require(__DIR__.'/element/container/Group.php');
			require(__DIR__.'/element/field/Text.php');
			require(__DIR__.'/element/rule/Required.php');
		}
		
		public function init($document=null)
		{
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
			//Create the top level document object.
			$this->document=new Document($this->json);
		}
		
		public function render()
		{
			if (is_null($this->document))
			{
				$this->parse();
			}
			return $this->document->render();
		}
	}
}
?>