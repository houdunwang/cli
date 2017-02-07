<?php
function cli( $cli ) {
	$_SERVER['argv'] = preg_split( '/\s+/', $cli );
	p($_SERVER['argv']);
	//执行命令行指令
	\Cli::bootstrap(true);
}