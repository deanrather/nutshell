<?php
/**
 * @package nutshell-plugin
 * @author guillaume
 */
namespace nutshell\plugin\spinAuth
{
	use nutshell\Nutshell;
	use nutshell\core\plugin\Plugin;
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\session\NamedSession;
	use \stdClass;
	
	/**
	 * @author guillaume
	 * @package nutshell-plugin
	 */
	class SpinAuth extends Plugin implements Native,Singleton,NamedSession
	{
		public static function loadDependencies()
		{
			
		}
		
		public static function registerBehaviours()
		{
			
		}
		
		public function init()
		{
			
		}
		
		public function login($username,$password)
		{
			//Dummy data!
			$response				=new stdClass();
			$response->success		=true;
			$response->code			=1;
			$response->token		='foo12345';
			
			//Save the token in the session and return the response.
			$this->session->token	=$response->token;
			return $response;
		}
	}
}
?>