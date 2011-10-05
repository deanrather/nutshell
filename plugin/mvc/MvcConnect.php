<?php
namespace nutshell\plugin\mvc
{
	use nutshell\Nutshell;

	/**
	 * This class implements methods that can be used as shortcuts to nutshell, plugins and models. 
	 * @author spinifex
	 * @package nutshell-plugin
	 * @abstract
	 */
	abstract class MvcConnect
	{
		/**
		* Stores a local pointer to Nutshell
		*/
		protected $nutshell = null;
		
		/**
		 * Stores a local pointer to plugin loader.
		 */
		protected $plugin	 = null;
		
		/**
		 * Defautl DB connection
		 * @var DB object
		 */
		protected $db	   =null;
		
		/**
		 * Logger used in models
		 * @var Logger
		 */
		private static $vLogger = null;
		
		public function __construct()
		{
			$this->nutshell = Nutshell::getInstance();
			if (isset($this->nutshell))
			{
				$this->plugin = $this->nutshell->plugin;
				if ($connection=$this->nutshell->config->plugin->Mvc->connection)
				{
					//Make a shortcut reference to the
					$this->db=$this->plugin->Db->{$connection};
				}
			}
		}
		
		/**
		 * returns a model loader
		 */
		private function model()
		{
			$result = null;
			if (isset($this->nutshell))
			{
				$mvc = $this->nutshell->plugin->Mvc();
				$result = $mvc->getModelLoader();
			}
			return $result;
		}
		
		/**
		 * This function returns the logger in use
		 */
		private function logger()
		{
			if (!isset(self::$vLogger))
			{
				self::$vLogger = $this->plugin->Logger();
			}
			return self::$vLogger;
		}

		public function __get($key)
		{
			if ($key=='model')
			{
				return $this->model(); // returns a model loader
			}
			elseif ($key=='logger')
			{
				return $this->logger(); // returns a logger
			}
		}
	}
}