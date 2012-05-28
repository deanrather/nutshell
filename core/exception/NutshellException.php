<?php
/**
 * @package nutshell
 */
namespace nutshell\core\exception
{
	use nutshell\Nutshell;
	use nutshell\core\Component;
	use \Exception;

	/**
	 * @package nutshell
	 */
	class NutshellException extends Exception
	{
		/*
		 * Error Codes
		 */
		
		/** The default error code. Please don't use this. Define your own error codes in your Exception Class */
		const GENERIC_ERROR		= 0;
		
		
		
		/**
		 * Receives an Error Code, and optionally one or many debug variables.
		 * The error code is for displaying to the user and identifying the exception type within the system
		 * The debug variables are for display in dev mode, and for logging
		 */
		public function __construct($code, $debug=null)
		{
			$this->code = $this->getCodePrefix().'-'.$code;
			
			// Set my 'debug' attribute
			$args = func_get_args();
			array_shift($args);
			$this->debug = $args;
		}
		
		
		/*
		 * Static Attributes
		 */
		
		/**
		* Prevents recursion.
		* @var bool
		*/
		private static $blockRecursion = false;
		
		/**
		 * Error handler used before the class is created.
		 * @var bool
		 */
		private static $oldErrorHandler = false;
		
		/**
		 * Indicates that errors should be shown. (depends on ns_env environment variable).
		 * @var bool
		 */
		private static $echoErrors = false;
		
		/**
		 * All errors that shouldn't be shown in the user interface.
		 * @var Array
		 */
		private static $dontShowErrors = array
		(
			E_STRICT => 1
		);
		
		
		
		
		/*
		 * Member Functions
		 */
		
		
		
		/**
		 * Gets the prefix to be used on error codes.
		 * If this is a NutshellException it will return Nutshell
		 * If this is a BTLException it will return BTL
		 * Doesn't return the namespace part
		 */
		private function getCodePrefix()
		{
			$className = get_class($this);
			$className = explode('\\', $className);
			$className = array_pop($className);
			$className = str_replace('Exception', '', $className);
			return $className;
		}
		
		
		/**
		 * Logs this exception
		 */
		public function log()
		{
			self::logMessage($this->getDescription());
		}
	
		
		/**
		 * Generates a nice desription of exception
		 * Good for returning to the client (in dev mode) or logging.
		 * @param Exception $exception the exception 
		 * @param String $format html or json
		 */
		public function getDescription($format='html')
		{
			$description = array
			(
				'ERROR'	=> true,
				'CLASS'	=> get_class($this),
				'CODE'	=> $this->code,
				'FILE'	=> $this->file,
				'LINE'	=> $this->line,
				'STACK'	=> "\n".$this->getTraceAsString(),
				'DEBUG'	=> var_export($this->debug, true)
			);
			
			if($format=='json')
			{
				$description = json_encode($message);
			}
			else
			{
				$temp = array();
				foreach($description as $key => $val)
				{
					$temp[] = "$key: $val";
				}
				$description = implode("\n", $temp);

				if($format=='html') $description = nl2br($description); // why this don't work
			}
			
			return $description;
		}
		
		
		/*
		 * Static Methods
		 */
		
		public static function register() 
		{
			Component::load(array());
		}
		
		
		/**
		 * Echoes an error if $echoErrors and not $dontShowErrors[$errno]
		 * @param int $errno
		 * @param string $message
		 */
		private static function echoError($errno, $message)
		{
			if (self::$echoErrors) 
			{
				if (!isset(self::$dontShowErrors[$errno]))
				{
					echo $message;
				}
			}
		}
		
		/**
		 * Logs a message if Nutshell has a loader.
		 * @param string $message
		 */
		public static function logMessage($message)
		{
			if (strlen($message)>0)
			{
				try
				{	
					$nutInst = Nutshell::getInstance();
					if ($nutInst->hasPluginLoader())
					{
						$log = $nutInst->plugin->Logger();
						$log->fatal($message);
					} 
					else 
					{
						user_error("Failed to load logger: $message", E_USER_ERROR);
					}
				}
				catch (Exception $e) 
				{
					//falling back to the system logger
					error_log($message);
				}
			}
		}
		
		/**
		 * This method treats (and logs) errors.
		 * @param int $errno
		 * @param string $errstr
		 * @param string $errfile
		 * @param int    $errline
		 * @param array $errcontext
		 */
		public static function treatError($errno, $errstr = null, $errfile = null, $errline = null, array $errcontext = null)
		{
			if (!self::$blockRecursion)
			{
				self::$blockRecursion = true;
			
				$message =
					"ERROR $errno. ".
					( (strlen($errstr)>0)  ? "Message: $errstr. " : "").
					( (strlen($errfile)>0) ? "File: $errfile. " : "").
					( ($errline>0) ? "Line: $errline. " : "") ;
				
				try // to log
				{
					self::echoError($errno, $message);
					self::logMessage($message);		
				} catch (Exception $e) 
				{
					//falling back to the system logger
					error_log($message);
				}
				self::$blockRecursion = false;
			}
			return false;
		}
		
		/**
		 * This method is called when an exception happens.
		 * @param Exception $exception
		 */
		public static function treatException($exception, $format='html')
		{
			if (!self::$blockRecursion)
			{
				self::$blockRecursion = true;
				
				$message = $exception->getDescription($format);
				
				self::logMessage($message);
				
				if (self::$echoErrors) 
				{
					header('HTTP/1.1 500 Application Error');
					echo $message;
				}
					
				self::$blockRecursion = false;
			}
		}
		
		/**
		 * This function sets exception/error handlers. Before this call, no error is treated by this class.
		 * Errors are shown in the user interface only if NS_ENV (environment variable) is set to "dev". So, errors won't be shown in production but will be logged.
		 * 
		 * Sets the default Exception Handler to treatException()
		 * Sets the default Error Handler to treatError()
		 */
		public static function setHandlers()
		{
			set_exception_handler('nutshell\core\exception\NutshellException::treatException');
			self::$oldErrorHandler = set_error_handler('nutshell\core\exception\NutshellException::treatError');
			self::$echoErrors = (NS_ENV=='dev');
		}
	}
}
