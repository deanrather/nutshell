<?php
/**
 * This namespace has been created to isolate php eval function execution in a restricted scope.
 */
namespace nutshell\plugin\format\strict
{
	/**
	 * Just executes an "eval" call.
	 * @param string $code
	 */
	function evalInStrictNameSpace($code)
	{
		eval($code);
	}
}