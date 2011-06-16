<?php
namespace nutshell\plugin\session
{
	use nutshell\Nutshell;

	use nutshell\plugin\session\exception\SessionException;

	use nutshell\core\plugin\Plugin;
	use nutshell\core\plugin\PluginExtension;
	
	use nutshell\behaviour\Native;
	use nutshell\behaviour\Singleton;
	use nutshell\behaviour\AbstractFactory;
	
	abstract class Handler extends PluginExtension
	{
		protected static $instance = null;
		
		protected $now = null;
		
		const DEFAULT_SESSION_TIMEOUT = 1200;
		
		const DEFAULT_ID_REGENERATION = 300;
		
		const SESSION_KEY_LAST_ACTIVITY = '__activity_ts';
		
		const SESSION_KEY_LAST_ID_REGEN = '__id_regen_ts';
		
		const SESSION_STORAGE_FILE = 'file';
		
		const SESSION_STORAGE_DB = 'db';
		
		protected $timeout = self::DEFAULT_SESSION_TIMEOUT;
		
		protected $idRegenRate = self::DEFAULT_ID_REGENERATION;
		
		public function init()
		{
			$this->now = time();
			$this->parseConfig();
			$this->initStorage();
			
			$this->start();
			
			//initialise the last activity timestamp if doesn't exist yet
			if(!isset($_SESSION[self::SESSION_KEY_LAST_ACTIVITY]))
			{
				$_SESSION[self::SESSION_KEY_LAST_ACTIVITY] = 0;
				$_SESSION[self::SESSION_KEY_LAST_ID_REGEN] = 0;
			}
			
			//check for session expiration
			if($this->now - $_SESSION[self::SESSION_KEY_LAST_ACTIVITY] > $this->timeout)
			{
				//discard and start a new session
				$this->destroy();
				$this->start();
				//force regenerate a new session ID
				$this->regenerateId();
			}
			
			//check for session expiration
			if($this->now - $_SESSION[self::SESSION_KEY_LAST_ID_REGEN] > $this->idRegenRate)
			{
				//force regenerate a new session ID
				$this->regenerateId();
			}
			
			//update the last activity timestamp
			$_SESSION[self::SESSION_KEY_LAST_ACTIVITY] = $this->now;
		}
		
		protected function parseConfig()
		{
			if(!is_null($this->config->timeout))
			{
				$this->timeout = $this->config->timeout;
			}
			
			if(!is_null($this->config->idRegenRate))
			{
				$this->idRegenRate = $this->config->idRegenRate;
			}
			
			if(!is_null($this->config->storage))
			{
				$this->storage = $this->config->storage;
			}
			
			return $this;
		}
		
		abstract protected function initStorage();
		
		public function regenerateId($deleteOldSession = null)
		{
			if(is_null($deleteOldSession) || !is_bool($deleteOldSession))
			{
				$deleteOldSession = false;
			}
			session_regenerate_id($deleteOldSession);
			$_SESSION[self::SESSION_KEY_LAST_ID_REGEN] = $this->now;
			return $this;
		}
		
		public function getId()
		{
			return session_id();
		}
		
		protected function start()
		{
			session_start();
			return $this;
		}
		
		public function destroy()
		{
			//force empty
			$_SESSION = array();
			//destroy
			session_destroy();
			return $this;
		}
		
		public function setTimeout($timeout)
		{
			$this->timeout = $timeout;
			return $this;
		}
		
		public function getTimeout()
		{
			return $this->timeout;
		}
		
		public function __get($key)
		{
			return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
		}
		
		public function __set($key, $value)
		{
			$_SESSION[$key] = $value;
		}
		
		public function __isset($key)
		{
			return isset($_SESSION[$key]);
		}
		
		public function __unset($key)
		{
			unset($_SESSION[$key]);
		}
	}
}
