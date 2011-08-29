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
	 * @package nutshell
	 * @static
	 */
	class ArrayClass
	{
		/**
		 * This function adds a column to a 2 dimensional array.
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

		/**
		 * This function renames a column in a 2 dimensional array.
		 * @param array
		 * @param string $old_column_name
		 * @param string $new_column_name
		 */
		public static function renameColumn(&$data, $old_column_name, $new_column_name)
		{
			if (($old_column_name != $new_column_name) && (strlen($old_column_name)>0) && (strlen($new_column_name)>0) )
			{
				foreach($data as &$row)
				{
					$row[$new_column_name] = $row[$old_column_name];
					unset($row[$old_column_name]);
				}
			}
		}
		
		/**
		 * This function returns an array where the $key is the same 
		 * @param array $aArrayToBeTransformed
		 */
		public static function createKeyEqualsValueArray($aArrayToBeTransformed)
		{
			$result = array ();
			foreach($aArrayToBeTransformed as $value)
			{
				$result[$value] = $value;
			}
			return $result;
		}
	}
}