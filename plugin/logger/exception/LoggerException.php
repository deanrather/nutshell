<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\logger\exception
{
	use nutshell\core\exception\NutshellException;

	/**
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell-plugin
	 */
	class LoggerException extends NutshellException
	{
		const INVALID_LEVEL = 1;
		const MISSING_OPTION = 2;
		const INVALID_WRITER = 3;
	} 
}