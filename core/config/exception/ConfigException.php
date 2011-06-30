<?php
namespace nutshell\core\config\exception
{
	use nutshell\core\exception\Exception;

	class ConfigException extends Exception
	{
		const CODE_INVALID_JSON = 1;
		
		const CODE_INVALID_CONFIG_FILE = 2;
		
		const CODE_CONFIG_FILE_NOT_FOUND = 3;
		
		const CODE_INVALID_NODE_EXTENSION = 4;
		
		const CODE_NON_PARSEABLE_STRUCTURE = 5;
	}
}