<?php
namespace nutshell\plugin\twiki
{
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\Nutshell;
	use nutshell\helper\String;

	/**
	 * Twiki means TEXTUAL Wiki. This plugin is based on the PEAR textual wiki.
	 */
	class Twiki extends Plugin implements Native,Singleton
	{
		protected $wiki;

		public static function loadDependencies()
		{
			require_once(__DIR__.'/lib/Text/Wiki.php');
		}

		public static function registerBehaviours()
		{
			
		}

		public function init()
		{
			$this->wiki = new \Text_Wiki();
		}
		
		/**
		 * This function transforms a wiki text into HTML text.
		 * @param string $text
		 */
		public function transform($text)
		{
			return $this->wiki->transform($text);
		}
		
		public function test()
		{
			/**
			 * The wiki text to be transformed into HTML.
			 * @var string
			 */
			$text =
'
[[toc]]
----
//emphasis text// 
**strong text**
//**emphasis and strong**//
{{teletype text}}
@@--- delete text +++ insert text @@
@@--- delete only @@
@@+++ insert only @@
----
			
+++  Level 3 Heading
	
++++ Level 4 Heading

+++++  Level 5 Heading

++++++ Level 6 Heading
			
----
			
* Bullet one
 * Sub-bullet
			 
# Numero uno
# Number two
 # Sub-item
 
# Number one
 * Bullet
 * Bullet
# Number two
 * Bullet
 * Bullet
  * Sub-bullet
   # Sub-sub-number
   # Sub-sub-number
# Number three
 * Bullet
 * Bullet
----
			
Shows a link: [http://pear.php.net PEAR]
			
Shows an image: http://c2.com/sig/wiki.gif

';
			// returns the HTML
			return $this->transform($text);
		}
	}
}