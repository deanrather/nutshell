<?php
namespace
{
	use nutshell\Nutshell;
	
	function bootstrap()
	{
		//Set the application path.
		Nutshell::setAppPath('../private/application/');
		//Initiate the MVC.
		Nutshell::getInstance()->plugin->Mvc();
	}
	require('../private/nutshell/Nutshell.php');
}
?>