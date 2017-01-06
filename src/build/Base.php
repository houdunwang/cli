<?php namespace houdunwang\cli\build;

/**
 * 命令行模式
 * Class Cli
 * @package hdphp\cli
 * @author 向军 <2300071698@qq.com>
 */
class Base {
	//绑定命令
	public $binds = [ ];

	/**
	 * 绑定命令
	 *
	 * @param string $name 命令标识
	 * @param \Closure $callback 闭包函数
	 */
	public function bind( $name, \Closure $callback ) {
		$this->binds[ $name ] = $callback;
	}

	//运行
	public function bootstrap() {
		if ( PHP_SAPI != 'cli' ) {
			return;
		}
		//去掉hd
		array_shift( $_SERVER['argv'] );
		$info = explode( ':', array_shift( $_SERVER['argv'] ) );
		//执行用户绑定的动作
		if ( isset( $this->binds[ $info[0] ] ) ) {
			array_unshift( $_SERVER['argv'], $this );
			call_user_func_array( $this->binds[ $info[0] ], $_SERVER['argv'] );
		}
		//命令类
		$class  = 'houdunwang\cli\\build\\' . strtolower( $info[0] ) . '\\' . ucfirst( $info[0] );
		$action = isset( $info[1] ) ? $info[1] : 'run';
		//实例
		if ( class_exists( $class ) ) {
			$instance = new $class();
			call_user_func_array( [ $instance, $action ], $_SERVER['argv'] );
			exit;
		} else {
			$this->error( 'Command does not exist' );
		}
	}

	//输出错误信息
	final public function error( $content ) {
		die( PHP_EOL . "\033[;36m $content \x1B[0m\n" . PHP_EOL );
	}

	//成功信息
	final public function success( $content ) {
		die( PHP_EOL . "\033[;32m $content \x1B[0m" . PHP_EOL );
	}
}


