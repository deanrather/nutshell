<?php
/**
 * This class has been written to give HipHop PHP
 * Serializable support.
 * 
 * @author Timothy Chandler <tim.chandler@spinifexgroup.com>
 * @since 21/03/2011
 * @version 1.0
 */
if (!interface_exists('Serializable'))
{
	interface Serializable
	{
		public function serialize();
		public function unserialize($serialized);
	}
}
?>