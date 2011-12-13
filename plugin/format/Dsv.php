<?php
namespace nutshell\plugin\format
{
	/**
	 * This class has been ported from Leadgen v1. It's previous name is Application_Model_Format_DSV.
	 */
	class Dsv extends FormatBase {
		
	    protected function writeRecord($batch) {
	    	if($this->_delimiter_escape) {
	    		$this->escapeDelimiter($batch);
	    	}
	
	        $this->writef(<<<EOL
%s

EOL
	        , implode($this->_delimiter, $batch)
	        );
	    }
	
	    protected function initHandler() {
	    	parent::initHandler();
	    	if (!is_null($this->getHeaders())) {
	    		$this->writeRecord($this->getHeaders());
	    	}
	    }
	
		protected function needsEscape($value) {
			return strpos($value, $this->_delimiter) !== false || strpos($value, "\n") !== false;
		}
	
		protected function escapeDelimiter(&$batch) {
	        foreach($batch as $i => $value) {
	        	if ($this->needsEscape($value)) {
	                $batch[$i] = $this->_text_qualifier . str_replace($this->_text_qualifier, $this->_escape_with . $this->_text_qualifier, $value) . $this->_text_qualifier;
	        	}
	        }
	    }
	}
}