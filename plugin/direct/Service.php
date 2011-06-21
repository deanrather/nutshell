<?php
namespace nutshell\plugin\direct
{
	use nutshell\core\exception\Exception;
	use nutshell\core\plugin\PluginExtension;
	
	class Service extends PluginExtension
	{
		const DEFAULT_INTERVAL	=3000;
		
		private $ref			=null;
		private $name			=null;
		private $description	=null;
		private $version		=null;
		
		public function __construct($ref)
		{
			$this->ref			=$ref;
			$this->name			=$this->config->{$this->ref}->name;
			$this->description	=$this->config->{$this->ref}->description;
			$this->version		=$this->config->{$this->ref}->version;
			
			
			//$this->buildDescriptor()
			
		}
		
		private function buildDescriptor()
		{
			$descriptor=array();
			foreach ($this->config->{$this->ref}->providers as $providerName=>$provider)
			{
				
				if ($provider->type=='polling')
				{
					$thisProvider=array
					(
						'type'		=>$provider->type,
						'url'		=>'?',
						'interval'	=>(isset($provider->interval))?$provider->interval:self::DEFAULT_INTERVAL,
					);
				}
				else if ($provider->type=='polling')
				{
					$thisProvider=array
					(
						'type'		=>$provider->type,
						'url'		=>'?',
						'namespace'	=>$this->namespace.'.'.$provider['namespace'],
						'actions'	=>array()
					);
				}
				else
				{
					throw new Exception('Invalid provider type "'.$provider->type.'".');
				}
				
				
				foreach ($provider->modules as $moduleName=>$module)
				{
					
				}
				$descriptor[]=$thisProvider;
			}
		}
	}
}
?>