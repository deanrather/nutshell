<?php
/**
 * @package nutshell
 */
namespace nutshell\core\exception
{
	use nutshell\core\Component;
	use nutshell\Nutshell;
	use \Exception as SPLException;
	
	/**
	 * @package nutshell
	 */
	class Exception extends SPLException
	{
		/**
		 * Prevents recursion.
		 * @var bool
		 */
		private static $blockRecursion = false;
		
		// logs the error message
		public function __construct ($message, $code = null, Exception $previous = null)
		{
			if (!self::$blockRecursion)
			{
				self::$blockRecursion = true;
				
				try // to log
				{
					$message  = (strlen($message)>0) ? "error message: $message" : "" ;
					$message .= ($code > 0) ? "error code: $code" : "" ;
					if (strlen($message)>0)
					{
						$nutInst = Nutshell::getInstance();
						if ($nutInst->hasLoader())
						{
							$log = $nutInst->plugin->Logger();
							$log->fatal("error message: $message, error code: $code");
						}
					}
				} catch (Exception $e) {
					// nothing can be done if log fails here.
				}
			}
			
			self::$blockRecursion = false;
			parent::__construct($message, $code, $previous);
		}
		
		public static function register() 
		{
			Component::load(array());
		}		
	}
}