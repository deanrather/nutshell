<?php
/**
 * @package nutshell-plugin
 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
 */
namespace nutshell\plugin\db\exception
{
	use nutshell\core\exception\PluginException;

	/**
	 * @author Guillaume Bodi <guillaume@spinifexgroup.com>
	 * @package nutshell-plugin
	 */
	class DbException extends PluginException
	{
		/** The database statement is malformed. */
		const INVALID_STATEMENT = 1;
	}
}