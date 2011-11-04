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
	class ArrayHelper
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
		 * This function renames a field in a 1 dimensional array.
		 * @param array  $row
		 * @param string $old_column_name
		 * @param string $new_column_name
		 */		
		public function renameField(array &$row, $old_field_name, $new_field_name)
		{
			$row[$new_field_name] = $row[$old_field_name];
			unset($row[$old_field_name]);
		}
		
		/**
		 * This function renames a multiple fields in a 1 dimensional array.
		 * @param array $row Data to be modified.
		 * @param array $aRenaming Array with multiple renamings. This parameter is passed as reference because it's faster. This array won't be modified.
		 */
		public function renameFields(array &$row, array &$aRenaming)
		{
			foreach($aRenaming as $old_field_name => $new_field_name)
			{
				self::renameField($row, $old_field_name, $new_field_name);
			}
		}

		/**
		 * This function renames a column in a 2 dimensional array.
		 * @param array
		 * @param string $old_column_name
		 * @param string $new_column_name
		 */
		public static function renameColumn(array &$data, $old_column_name, $new_column_name)
		{
			if (($old_column_name != $new_column_name) && (strlen($old_column_name)>0) && (strlen($new_column_name)>0) )
			{
				foreach($data as &$row)
				{
					self::renameField($row, $old_column_name, $new_column_name);
				}
			}
		}
		
		/**
		 * This function returns an array where the $key is the same 
		 * @param array $aArrayToBeTransformed
		 */
		public static function createKeyEqualsValueArray(array $aArrayToBeTransformed)
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