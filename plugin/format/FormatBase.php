<?php
namespace nutshell\plugin\format
{
	/** 
	 * This class is a base for (is extended by) all formating classes.
	 */
	class FormatBase
	{
		const WORK_FILE_PREFIX = 'nuttmp_';
	
		protected $_format_name;
	
		protected $_work_file_name;
	
		protected $_file_out;
	
		protected $_file_handle = null;
	
		protected $_file_extension;
	
		protected $_headers;
	
		const LINE_BREAK_WIN = 'CRLF';
	
		const LINE_BREAK_UNIX = 'LF';
	
		protected $_format_config_line_break = self::LINE_BREAK_UNIX;
	
		protected $_format_config_file_name_override;
	
		protected static $_line_breaks = null;
	
		protected static $_formats = null;
		
		public function __construct($values = array()) {
			
		}
	
		public static function getAvailableLineBreaks() {
			if (is_null(self::$_line_breaks)) {
				self::$_line_breaks = array();
				self::$_line_breaks[self::LINE_BREAK_UNIX] = self::translate('Mac/Unix (LF)');
				self::$_line_breaks[self::LINE_BREAK_WIN] = self::translate('Windows (CRLF)');
			}
			 
			return array_merge(array(), self::$_line_breaks);
		}
	
		public function getHeaders()
		{
			return $this->_headers;
		}
		 
		public function setHeaders($value)
		{
			$this->_headers = $value;
			return $this;
		}
		 
		public function getFormat_class()
		{
			return get_class($this);
		}
		 
		protected function setFormat_class($value)
		{
			//ignore
			return $this;
		}
		 
		public function getFormat_name()
		{
			return $this->_format_name;
		}
		 
		public function setFormat_name($value)
		{
			$this->_format_name = $value;
			return $this;
		}
		 
		public function setFormat_config_line_break($value) {
			if (is_null($value) || empty($value)) {
				$value = self::LINE_BREAK_UNIX;
			}
			if (array_key_exists($value,self::getAvailableLineBreaks())) {
				$this->_format_config_line_break = $value;
			} else {
				throw new Exception('Invalid parameter');
			}
			return $this;
		}
		 
		public function getFormat_config_line_break() {
			return $this->_format_config_line_break;
		}
		 
		public function getFormat_config_file_name_override()
		{
			return $this->_format_config_file_name_override;
		}
		 
		public function setFormat_config_file_name_override($value)
		{
			$this->_format_config_file_name_override = $value;
			return $this;
		}
		 
		public function getWork_file_name()
		{
			return $this->_work_file_name;
		}
		 
		protected function setWork_file_name($value)
		{
			$this->_work_file_name = $value;
			return $this;
		}
		 
		public function getFile_out()
		{
			return $this->_file_out;
		}
		 
		public function setFile_out($value)
		{
			if (preg_match('/\.[a-zA-Z0-9]{3,4}$/', $value)) {
				//the user defined its own file extension
				$this->_file_out = $value;
			} else {
				//use the default file extension
				$this->_file_out = sprintf('%s.%s', $value, $this->file_extension);
			}
			return $this;
		}
		 
		protected function getFile_handle()
		{
			return $this->_file_handle;
		}
		 
		protected function setFile_handle($value)
		{
			$this->_file_handle = $value;
			return $this;
		}
		 
		public function getFile_extension()
		{
			return $this->_file_extension;
		}
		 
		protected function setFile_extension($value)
		{
			$this->_file_extension = $value;
			return $this;
		}
		 
		/*
		 * General methods for file output
		*/
		 
		protected final function open_file() {
			// in the case that there is an open filed, closes it.
			$this->close_file();
			
			$this->file_handle = fopen($this->work_file_name, 'w');
			if (!$this->file_handle) {
				throw new Exception("Can't open the file for edition.");
			}
			$this->initHandler();
		}
		 
		public final function new_file() 
		{
			$this->work_file_name = APP_HOME . 'cache' ._DS_ . self::WORK_FILE_PREFIX . mt_rand() . '.' . $this->file_extension ;
			$this->open_file();
		}
		 
		public final function process(array $records) {
			foreach($records as $record) {
				$this->writeRecord($record);
			}
		}
		 
		public final function close_file() {
			if (isset($this->file_handle))
			{
				if ($this->file_handle) 
				{
					$this->closeHandler();
					fclose($this->file_handle);
					unset($this->file_handle);
				}
			}
		}
		
		public final function delete()
		{
			$this->close_file();
			unlink($this->work_file_name);
		}
		 
		/*
		 * Methods to override in implementations
		 */
		
		/**
		 * Utility method to write a formatted string to the currently opened file
		 */
		protected function writef() {
			$args = func_get_args();
			$content = call_user_func_array('sprintf', $args);
			fwrite($this->file_handle, $this->updateLineBreaks($this->getFormat_config_line_break(), $content));
		}
		 
		protected static function updateLineBreaks($toStyle, $txt) {
			switch($toStyle) {
				case self::LINE_BREAK_WIN:
					return preg_replace('/\r\n|\r|\n/', "\r\n", $txt);
				case self::LINE_BREAK_UNIX:
				default:
					return str_replace(array("\r\n", "\r"), array("\n", "\n"), $txt);
			}
		}
		 
		protected function writeRecord($batch) {
			$this->writef(serialize($batch));
		}
		 
		protected function initHandler() {
			//default do nothing
		}
		 
		protected function closeHandler() {
			//default do nothing
		}
		 
		public function __toString() {
			return $this->getFormat_name();
		}
		 
		public function getFormat_config() {
			$array = array();
			$this->push_config($array);
			return $array;
		}
		 
		public function setFormat_config($values) {
			if(is_string($values)) {
				$values = json_decode($values, true);
			}
			$this->setAll($values);
			return $this;
		}
		 
		public function toArray() {
			$array = array();
			$array['format_class'] = $this->format_class;
			$array['format_name'] = $this->format_name;
				
			$this->push_config($array);
			return $array;
		}
		 
		protected function push_config(array &$data) {
			$data['format_config_line_break'] = $this->format_config_line_break;
			$data['format_config_file_name_override'] = $this->format_config_file_name_override;
		}
	}
}