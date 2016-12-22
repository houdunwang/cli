<?php
require 'vendor/autoload.php';
$obj = new \houdunwang\cli\Cli();
$obj->bind( 'hd', function ( $cli, $arg1, $arg2 ) {
	echo $arg1, $arg2;
	$cli->error( '操作成功' );
} );
$obj->bootstrap();
