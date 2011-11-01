<?php
namespace nutshell\plugin\format
{
	/**
	 * This class has been ported from Leadgen v1. Its previous name is Application_Model_Format_XML.
	 */
	class Xml extends FormatBase {
	    
		const ROOT_ELEMENT = 'xml';
		
		const LEAD_ELEMENT = 'record';
		
	    public function __construct($values = array()) {
	        parent::__construct($values);
	        
	        $this->format_name = 'XML';
	        $this->file_extension = 'xml';
	    }
		
	    protected function writeRecord($batch) {
	    	$s = <<<EOL
    <%1\$s>
%2\$s
    </%1\$s>

EOL;
	        $this->writef($s, self::LEAD_ELEMENT, $this->buildRecordString($batch) );
	    }
	    
	    protected function initHandler() {
	    	parent::initHandler();
	    	$s = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<%s>

EOL;
	        $this->writef( $s, self::ROOT_ELEMENT );
	    }
	    
	    protected function closeHandler() {
	    	$s = <<<EOL
</%s>
EOL;
	        $this->writef($s, self::ROOT_ELEMENT);
	        parent::closeHandler();
	    }
	    
	    protected function buildRecordString($batch) {
	    	$text = '';
	    	
	    	foreach($batch as $key => $value) {
	    		$s = <<<EOL
	<%1\$s><![CDATA[%2\$s]]></%1\$s>

EOL;
	    		$text .= sprintf($s, $key, $value);
	    	}
	    	
	    	return $text;
	    }
	}
}