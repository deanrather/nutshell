<?php
namespace nutshell\behaviour
{
	interface Native
	{
		public static function loadDependencies();
		
		public static function registerBehaviours();
	}
}
?>