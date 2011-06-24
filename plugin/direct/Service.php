<?php
namespace nutshell\plugin\direct
{
	use nutshell\helper\Object;

	use nutshell\plugin\mvc\Controller;
	use nutshell\core\exception\Exception;
	use nutshell\core\plugin\PluginExtension;
	
	class Service extends PluginExtension
	{
		const DEFAULT_INTERVAL	=3000;
		
		private $controller		=null;
		private $ref			=null;
		private $name			=null;
		private $description	=null;
		private $version		=null;
		private $nsPrefix		=null;
		
		public function __construct(Controller $controller,$ref)
		{
			parent::__construct();
			$this->controller	=$controller;
			$this->ref			=$ref;
			$this->name			=$this->config->{$this->ref}->name;
			$this->description	=$this->config->{$this->ref}->description;
			$this->version		=$this->config->{$this->ref}->version;
			$this->nsPrefix		=$this->config->{$this->ref}->nsPrefix;
			
			
		}
		
		public function buildDescriptor()
		{
			$descriptor=array();
			foreach ($this->config->{$this->ref}->providers as $providerName=>$provider)
			{
				
				if ($provider->type=='polling')
				{
					$thisProvider=array
					(
						'type'		=>$provider->type,
						'url'		=>$this->plugin->Url->makeURL(strtolower(Object::getBaseClassName($this->controller)).'/'.$providerName),
						'interval'	=>(isset($provider->interval))?$provider->interval:self::DEFAULT_INTERVAL,
					);
				}
				else if ($provider->type=='remoting')
				{
					$thisProvider=array
					(
						'type'		=>$provider->type,
						'url'		=>$this->plugin->Url->makeURL(strtolower(Object::getBaseClassName($this->controller)).'/'.$providerName),
						'namespace'	=>$this->nsPrefix.'.'.$providerName,
						'actions'	=>array()
					);
				}
				else
				{
					throw new Exception('Invalid provider type "'.$provider->type.'".');
				}
				foreach ($provider->modules as $moduleName=>$module)
				{
					for ($i=0,$j=count($module); $i<$j; $i++)
					{
						$thisProvider['actions'][$moduleName][]=array
						(
							'name'	=>$module[$i]->name,
							'len'	=>$module[$i]->args
						);
					}
				}
				$descriptor[]=$thisProvider;
			}
			print json_encode($descriptor);
		}
		
		public function processRequest()
		{
			var_dump($GLOBALS['HTTP_RAW_POST_DATA']);
			if (count($_POST))
			{
				//Non-Batch
//				if ()
//				{
//					$this->handleRequest($this->global->post());
//				}
//				//Batch
//				else
//				{
//					for ($i=0,$j=count($this->global->post()); $i<$j; $i++)
//					{
//						$this->handleRequest($this->global->post($i));
//					}
//				}
			}
			//Polling Request
			else
			{
//				include_once(APP_HOME.$this->config->dir->providers.$this->plugin->Url->node(2).'.php');
//				$moduleName	=?;
//				$module		=new $moduleName($this->scope,$this->responder,null);
			}
			$this->responder->send();
		}
		
		public function providerExists($provider=false)
		{
			//Remoting Provider
			if (is_dir(APP_HOME.$this->config->dir->providers.$provider)
			//Polling Provider
			|| is_file(APP_HOME.$this->config->dir->providers.$provider.'php'))
			{
				return true;
			}
			return false;
		}
	}
}
?>