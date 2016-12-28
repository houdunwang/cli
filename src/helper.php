<?php
if ( ! function_exists( 'cli' ) ) {
	/**
	 * 通过函数执行命令行指令
	 */
	function cli() {
		$argv[] = 'hd';
		foreach ( func_get_args() as $v ) {
			$argv[] = $v;
		}
		$_SERVER['argv'] = $argv;
		\houdunwang\cli\Cli::bootstrap();
	}
}