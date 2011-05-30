<?php
namespace nutshell\helper
{
	class String
	{
		/**
		 * Gernates a random string with or without numeric characters.
		 * 
		 * Note that the first character is never numeric.
		 * 
		 * @access public
		 * @param Int $length - The length of the randomly generated string to generate.
		 * @param Boolean $numbers - Weather or not to include numbers.
		 * @return String - Returns a randomly generated string.
		 */
		public static function getRandomString($length=10,$numbers=true)
		{
			//Force length to be an integer.
			$length=(int)$length;
			//Generate characters in an array A-Z.
			$chars=range('a','z');
			//Include numbers?
			if ($numbers)
			{
				//Merge numbers into the character array.
				$chars=array_merge($chars,range(0-9));
			}
			//Cache the length.
			$charLength=count($chars)-1;
			//Create an empty string to return.
			$string='';
			//Set the first character flag to true.
			$first=true;
			//Loop until length is 0.
			while ($length)
			{
				//Make sure the first character is never a number.
				if ($first)
				{
					$char=0;
					while (is_numeric($char))
					{
						$char=$chars[mt_rand(0,$charLength)];
					}
					$first=false;
				}
				else
				{
					$char=$chars[mt_rand(0,$charLength)];
				}
				//Add $char to the string.
				$string.=$char;
				//Remove 1 from $length - This will cause the loop to stop if length reaches 0.
				$length--;
			}
			//Return the randomly generated string.
			return $string;
		}
	}
}
?>