<?php
namespace nutshell\plugin\formatParser
{
	class Dsv extends FormatParserBase
	{
		const ESCAPE_MODE_DOUBLING = 1;

		const ESCAPE_MODE_CHARACTER = 2;

		const DEFAULT_ESCAPE_CHARACTER = '\\';

		protected $headers = array();

		protected $fieldDelimiter = null;

		protected $stringDelimiter = null;

		protected $recordDelimiter = self::LINE_ENDING_UNIX;

		protected $escapeMode = self::ESCAPE_MODE_CHARACTER;

		protected $escapeCharacter = self::DEFAULT_ESCAPE_CHARACTER;

		public function __construct($values = array()) {
			parent::__construct($values);
		}

		public function setRecordDelimiter($delimiter)
		{
			$this->recordDelimiter = $delimiter;
		}

		public function setEscapeMode($escapeMode)
		{
			$this->escapeMode = $escapeMode;
		}

		public function setEscapeCharacter($escapeCharacter)
		{
			$this->escapeCharacter = $escapeCharacter;
		}

		// we need to override the preProcessor to parse the column headers
		protected function preProcess(&$returnValue, &$content)
		{
			$this->readHeaders($content);
		}

		protected function process(&$returnValue, &$content)
		{
			while(!is_null($rawRecord = $this->readRawRecord($content)))
			{
				$values = $this->readValues($rawRecord);
				$filling = count($this->headers) - count($values);
				if($filling > 0) 
				{
					$values = array_merge($values, 
						array_fill(0, count($this->headers) - count($values), '')	
					);
				}

				$values = array_combine($this->headers, $values);

				$returnValue[] = $values;
			}
		}

		protected function readHeaders(&$content)
		{
			$this->headers = $this->readValues($this->readRawRecord($content));
		}

		protected function readValues($rawRecord)
		{
			$values = array();
			$rawSplit = split($this->fieldDelimiter, $rawRecord);
			$accumulator = array();

			foreach($rawSplit as $val)
			{
				$accumulator[] = $val;
				$candidateValue = implode($this->fieldDelimiter, $accumulator);
				if($this->isComplete($candidateValue))
				{
					$values[] = $this->unescape($candidateValue);
					$accumulator = array();
				}
			}

			return $values;
		}

		protected function unescape($escapedValue)
		{
			if(substr($escapedValue, 0, 1) != $this->stringDelimiter)
			{
				// no need to unescape if it's not a string marker enclosed value
				return $escapedValue;
			}

			// by now it is assumed the format is correct, 
			// so just strip the first and last characters
			$escapedValue = substr($escapedValue, 1, strlen($escapedValue) - 2);

			// now depending on the escaping mode, we have more transformations to do
			switch($this->escapeMode)
			{
				case self::ESCAPE_MODE_CHARACTER:
					$escapedValue = str_replace(
						array(
							$this->escapeCharacter . $this->stringDelimiter,
							$this->escapeCharacter . $this->escapeCharacter
						), 
						array(
							$this->stringDelimiter,
							$this->escapeCharacter
						),
						$escapedValue
					);
					break;
					
				case self::ESCAPE_MODE_DOUBLING:
					$escapedValue = str_replace(
						$this->stringDelimiter . $this->stringDelimiter, 
						$this->stringDelimiter,
						$escapedValue
					);
					break;
			}

			return $escapedValue;
		}

		protected function readRawRecord(&$content)
		{
			if($content === '')
			{
				return null;
			}

			$rawRecordStack = array();
			do {
				list($head, $content) = split($this->recordDelimiter, $content, 2);
				$rawRecordStack[] = $head;
			} while(!$this->isComplete(
				implode($this->recordDelimiter, $rawRecordStack)
			));

			return implode($this->recordDelimiter, $rawRecordStack);
		}

		protected function isComplete($rawRecordString)
		{
			switch($this->escapeMode)
			{
				case self::ESCAPE_MODE_CHARACTER:
					// remove all escapes of the escape character itself
					// and the escaped string delimiter
					$rawRecordString = str_replace(
						array(
							$this->escapeCharacter . $this->escapeCharacter,
							$this->escapeCharacter . $this->stringDelimiter
						), 
						'', 
						$rawRecordString
					);

					// fall through since the processing is the same now
					// MUST BE FOLLOWED BY THE DOUBLING ESCAPE MODE LOGIC

				case self::ESCAPE_MODE_DOUBLING:
					// no string delimiter provided -> we have to assume it's true
					if(is_null($this->stringDelimiter) || $this->stringDelimiter == '')
					{
						return true;
					}
					
					// we just need to make sure that the string delimiter count is even
					return substr_count($rawRecordString, $this->stringDelimiter) % 2 == 0;

			}

			return false;
		}
		
	}
}