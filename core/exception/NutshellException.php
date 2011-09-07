<?php
/**
 * @package nutshell
 */
namespace
{
	use nutshell\Nutshell;

	/**
	 * @package nutshell
	 */
	class NutshellException extends Exception
	{
		
		/**
		* Prevents recursion.
		* @var bool
		*/
		private static $blockRecursion = false;
		
		
		/**
		 * Error handler used before the class is created.
		 */
		private static $oldErrorHandler = false;
		
		/**
		 * Indicates that errors should be shown. (depends on ns_env environment variable).
		 * @var bool
		 */
		private static $echoErrors = false;
		
		/**
		 * All errors that shouldn't be shown in the user interface.
		 * @param unknown_type $message
		 */
		private static $dontShowErrors = array
		(
			E_STRICT => 1
		);

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
		private static function logMessage($message)
		{
			if (strlen($message)>0)
			{
				$nutInst = Nutshell::getInstance();
				if ($nutInst->hasLoader())
				{
					$log = $nutInst->plugin->Logger();
					$log->fatal($message);
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
			
				try // to log
				{
					$message =
						"ERROR $errno. ".
						( (strlen($errstr)>0)  ? "Message: $errstr. " : "").
						( (strlen($errfile)>0) ? "File: $errfile. " : "").
						( ($errline>0) ? "Line: $errline. " : "") ;
						self::echoError($errno, $message);
					self::logMessage($message);		
				} catch (Exception $e) 
				{
					// nothing can be done if log fails here.
				}
				self::$blockRecursion = false;
			}
			return false;
		}
		
		/**
		 * This method is called when an exception happens.
		 * @param Exception $exception
		 */
		public static function treatException($exception)
		{
			if (!self::$blockRecursion)
			{
				self::$blockRecursion = true;
			
				try // to log
				{
					$message =
						( ($exception->code>0)             ? "ERROR {$exception->code}. " : "" ).
						( "Exception class:".get_class($exception).". ").
						( (strlen($exception->message)>0)  ? "Message: {$exception->message}. " : "").
						( (strlen($exception->file)>0)     ? "File: {$exception->file}. " : "").
						( ($exception->line>0)             ? "Line: {$exception->line}. " : "") ;
						
					if (self::$echoErrors) 
					{
						echo $message;
					}
					self::logMessage($message);		
				} catch (Exception $e) 
				{
					// nothing can be done if log fails here.
				}
				self::$blockRecursion = false;
			}
		}
		
		public static function setHandlers()
		{
			set_exception_handler('NutshellException::treatException');
			self::$oldErrorHandler = set_error_handler('NutshellException::treatError');
			self::$echoErrors = ((getenv('NS_ENV')=='dev') || (apache_getenv("NS_ENV") == 'dev'));
		}
	}

	NutshellException::setHandlers();
}