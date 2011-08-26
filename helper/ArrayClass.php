<?php
/**
 * @package nutshell
 * @author guillaume
 */
namespace nutshell\helper
{
	/**
	 * The Array helper class.
	 * 
	 * This class has specialized methods for dealing 
	 * with arrays.
	 *  
	 * @author guillaume
	 * @package nutshell
	 * @static
	 */
	class ArrayClass
	{
		/**
		 * This function adds a column to a 2 dimensioinal array.
		 * @param array $data
		 * @param string $column_name
		 * @param mixed $value
		 */
		public static function addColumnWithValue(&$data, $column_name, $value)
		{
			foreach($data as &$row)
			{
				$row[$column_name] = $value;
			}
		}
	}
}