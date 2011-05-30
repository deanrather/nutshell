<?php
/**
 * This class has been written to give HipHop PHP
 * IteratorAggregate support.
 * 
 * @author Timothy Chandler <tim.chandler@spinifexgroup.com>
 * @since 21/03/2011
 * @version 1.0
 */
if (!interface_exists('IteratorAggregate'))
{
	interface IteratorAggregate extends Traversable
	{
		
	}
}
?>