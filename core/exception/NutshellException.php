<?php
/**
 * @package nutshell
 * @author Dean Rather
 */
namespace nutshell\core\exception
{
	use nutshell\Nutshell;
	use nutshell\core\Component;
	use nutshell\core\config\exception\ConfigException;
	use \Exception;
	
	class NutshellException extends Exception
	{
		/*
		 * Error Codes
		 */
		
		/** The default error code. Please don't use this. Define your own error codes in your Exception Class */
		const GENERIC_ERROR				= 0;
		
		/** A regular PHP error */
		const PHP_ERROR					= 1;
		
		/** A Fatal php error */
		const PHP_FATAL_ERROR			= 2;
		
		const INVALID_APP_PATH			= 10;
		const INVALID_PROPERTY			= 11;
		const READ_ONLY					= 12;
		
		/*
		 * Instance Properties
		 */
		
		public $codeDescription	= '';
		public $code			= '';
		public $debug			= '';
		
		/**
		 * Receives an Error Code, and optionally one or many debug variables.
		 * The error code is for displaying to the user and identifying the exception type within the system
		 * The debug variables are for display in dev mode, and for logging
		 */
		public function __construct($code=0, $debug=null)
		{
			$debug = func_get_args();
			
			/**
			 * If they didn't pass in an exception code:
			 * treat all input as debug info,
			 * set exception code to 0
			 */
			if(!is_int($code))
			{
				$code = self::GENERIC_ERROR;
			}
			else
			{
				array_shift($debug);
			}
			
			// Get the code's description using Reflection
			$reflection = new \ReflectionClass($this);
			$constants = $reflection->getConstants();
			$this->codeDescription = array_search($code, $constants);
			
			// Alter the code to be prefixed with the class name
			$this->code = $this->getCodePrefix().'-'.$code;
			
			// store the debug info
			$this->debug = $debug;
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
		 * Error handler used before the class was created.
		 * @var bool
		 */
		private static $oldErrorHandler = false;
		
		/**
		 * Exception handler used before the class was created.
		 * @var bool
		 */
		private static $oldExceptionHandler = false;
		
		
		
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
		 * Logs the result of getDescription()
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
			$debug = $this->debug;
			if($format != 'array')
			{
				// don't use var_export. it can cause a recursive error here.
				$debug = print_r($debug, true); 
			}
			
			$description = array
			(
				'ERROR'				=> true,
				'CLASS'				=> get_class($this),
				'CODE'				=> $this->code,
				'CODE_DESCRIPTION'	=> $this->codeDescription,
				'FILE'				=> $this->file,
				'LINE'				=> $this->line,
				'DEBUG'				=> $debug,
				'STACK'				=> "\n".$this->getTraceAsString(),
                                'SERVER'                        => $_SERVER,
                                'POST'                          => $_POST,
                                'GET'                           => $_GET,
                                'RAW'                           => Nutshell::getInstance()->request->getRaw()
			);
			
			if($format=='array')
			{
				$description['STACK'] = $this->getTrace();
			}
			elseif($format=='json')
			{
				$description = json_encode($description);
			}
			elseif($format=='html')
			{
				$description = '<pre>'.print_r($description, true).'</pre>';
			}
			else
			{
				// Display in a sensible way for logging.
				$temp = array();
				foreach($description as $key => $val)
				{
					$temp[] = "$key: $val";
				}
				$description = implode("\n", $temp);
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
		 * Logs a message if Nutshell has a loader.
		 * @param string $message
		 */
		public static function logMessage($message)
		{
			if (!strlen($message)) return;
			try
			{
				$nutInst = Nutshell::getInstance();
				if ($nutInst->hasPluginLoader())
				{
					$log = $nutInst->plugin->Logger();
					$log->fatal($message); // todo, sometimes it's not 'fatal'
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
			$exception = new NutshellException(self::PHP_ERROR, $errstr);
			$exception->code = $errno;
			$exception->file = $errfile;
			$exception->line = $errline;
			self::treatException($exception);
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
				
				// Create the message
				if($exception instanceof ConfigException)
				{
					die('ERROR: ' . $exception->getCode() .' '. $exception->debug[0]);
				}
				elseif($exception->code == E_STRICT)
				{
					// The logger fails to load in the case of a "<function name>  should be compatible with that of <parent function name>" error
					die('ERROR: ' . $exception->getCode() .' '. $exception->debug[0]);
				}
				elseif($exception instanceof LoggerException)
				{
					die('ERROR: ' . $exception->getCode() .' '. $exception->debug[0]);
				}
				elseif($exception instanceof NutshellException)
				{
					$message = $exception->getDescription($format);
				}
				else
				{
					$message = "NON NUTSHELL EXCEPTION! ";
					$message .= $exception->getTraceAsString();
					$message .= nl2br($exception);
				}
				
				// Log the message
				self::logMessage($message);
				
				// Echo the message
				if(Nutshell::getInstance()->config->application->mode=='development')
				{
					header('HTTP/1.1 500 Application Error');
					echo $message;
				}
					
				self::$blockRecursion = false;
			}
		}
		
		/**
		 * This function is called when PHP is shutting down.
		 * It will get called after a fatal error, so we'll try and handle that here.
		 * You can't really handle fatal errors, but we can try, and might at least
		 * get a log out of it.
		 */
		public static function shutdown()
		{ 
			$error=error_get_last();
			if($error)
			{
				self::treatError
				(
					self::PHP_FATAL_ERROR,
					$error['message'],
					$error['file'],
					$error['line']
				);
			}
		}
		
		/**
		 * This function sets exception/error handlers. Before this call, no error is treated by this class.
		 * All errors are logged.
		 * Errors are shown in the user interface only if NS_ENV (environment variable) is set to "dev". So, errors won't be shown in production.
		 * 
		 * Sets the default Exception Handler to treatException()
		 * Sets the default Error Handler to treatError()
		 */
		public static function setHandlers()
		{
			self::$oldExceptionHandler = set_exception_handler('nutshell\core\exception\NutshellException::treatException');
			self::$oldErrorHandler = set_error_handler('nutshell\core\exception\NutshellException::treatError');
			register_shutdown_function('nutshell\core\exception\NutshellException::shutdown'); 
		}
	}
}
