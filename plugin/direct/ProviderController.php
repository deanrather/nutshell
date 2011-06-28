<?php
namespace nutshell\plugin\direct
{
	use nutshell\Nutshell;

	class ProviderController
	{
		const CODE_FAIL		=0;
		const CODE_SUCCESS	=1;
		
		private $core		=null;
		private $responder	=null;
		
		private $response	=array
		(
			'tid'			=>null,
			'type'			=>null,
			'action'		=>null,
			'method'		=>null,
			'name'			=>null,
			'result'		=>null,
			'where'			=>null,
			'timestamp'		=>null
		);
		
		private $result=array
		(
			'success'	=>false,
			'message'	=>null,
			'code'		=>self::CODE_FAIL,
			'data'		=>null
		);
		
		public function __construct(Responder $responder,$request)
		{
			$this->core		=Nutshell::getInstance();
			$this->responder=$responder;
			
			$this->response['tid']		=$request->tid;
			$this->response['type']		=$request->type;
			$this->response['action']	=$request->action;
			$this->response['method']	=$request->method;
			$this->response['timestamp']=time();
			
			//Link the response result to result.
			$this->response['result']=&$this->result;
		}
		
		public function respond(Array $response=array())
		{
			$this->response=$response;
			return $this;
		}
		
		public function success($message=null,$code=self::CODE_SUCCESS)
		{
			$this->result['success']	=true;
			$this->result['message']	=$message;
			$this->result['code']		=$code;
			return $this;
		}
		
		public function fail($message=null,$code=self::CODE_FAIL)
		{
			$this->result['success']	=false;
			$this->result['message']	=$message;
			$this->result['code']		=$code;
			return $this;
		}
		
		public function setResponseCode($code)
		{
			$this->result['code']		=$code;
			return $this;
		}
		
		public function setResponseMessage($message)
		{
			$this->result['message']		=$message;
			return $this;
		}
		
		public function setResponseData($data)
		{
			$this->result['data']		=$data;
			return $this;
		}
		
		public function sendResponse()
		{
			$this->responder->push($this->response);
			return $this;
		}
	}
}
?>