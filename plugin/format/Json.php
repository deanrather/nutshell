<?php
namespace nutshell\plugin\format
{
	/**
	 * This class has been ported from Leadgen v1. Its previous name is Application_Model_Format_JSON.
	 */
	class Json extends FormatBase {
	    
		const RECORD_SEPARATOR = ',';
		
		protected $_count = 0;
		
	    public function __construct($values = array()) {
	        parent::__construct($values);
	        
	        $this->format_name = 'JSON';
	        $this->file_extension = 'json';
	    }
		
	    protected function writeRecord($batch) {
	        $this->writef(<<<EOL
%s%s
EOL
	        , $this->_count > 0 ? self::RECORD_SEPARATOR . "\n" : ''
	        , json_encode($batch)
	        );
	        $this->_count++;
	    }
	    
	    protected function initHandler() {
	    	parent::initHandler();
	    	$this->_count = 0;
	        $this->writef(<<<EOL
[

EOL
        );

    }
	    
	    protected function closeHandler() {
	        $this->writef(<<<EOL

]
EOL
	        );
	        parent::closeHandler();
	    }
	    
	    protected function buildRecordString() {
	    	
	    }
	}
}