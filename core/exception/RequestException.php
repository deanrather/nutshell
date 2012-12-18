<?php
/**
 * @package nutshell
 * @author Dean Rather
 */
namespace nutshell\core\exception
{
	use nutshell\core\exception\NutshellException;

	class RequestException extends NutshellException
	{
		const MUST_PROVIDE_ARGS = 1;
	}
}