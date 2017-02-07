<?php
function cli( $cli ) {
	$args = preg_split( '/\s+/', $cli );
	$args = array_unshift( $args, 'hd' );
	p( $args );
}