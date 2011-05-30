<?php
/**
 * This class has been written to give HipHop PHP
 * ArrayAccess support.
 * 
 * @author Timothy Chandler <tim.chandler@spinifexgroup.com>
 * @since 21/03/2011
 * @version 1.0
 */
if (!interface_exists('ArrayAccess'))
{
	interface ArrayAccess
	{
		public function offsetGet($offset);
		public function offsetSet($offset,$value);
		public function offsetExists($offset);
		public function offsetUnset($offset);
	}
}
?>