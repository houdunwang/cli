<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDPHP framework]
 * |      Site: www.hdphp.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.hdphp.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/
namespace houdunwang\cli\build\migrate;

use houdunwang\cli\build\Base;

class Migrate extends Base {
	//当前执行的数据库中的编号
	protected static $batch;

	public function __construct() {
		if ( empty( self::$batch ) ) {
			self::$batch = Db::table( 'migrations' )->max( 'batch' ) ?: 0;
		}
	}

	//执行迁移
	public function make() {
		$files = glob( ROOT_PATH . '/system/database/migrations/*.php' );
		sort( $files );
		foreach ( (array) $files as $file ) {
			$name = substr( basename( $file ), 0, - 4 );
			//只执行没有执行过的migration
			if ( ! Db::table( 'migrations' )->where( 'migration', $name )->first() ) {
				require $file;
				$class = 'system\database\migrations\\' . substr( basename( $file ), 18, - 4 );
				( new $class )->up();
				Db::table( 'migrations' )->insert( [ 'migration' => $name, 'batch' => ++ self::$batch ] );
			}
		}
	}

	//回滚到上次迁移
	public function rollback() {
		$batch = Db::table( 'migrations' )->max( 'batch' );
		$files = Db::table( 'migrations' )->where( 'batch', $batch )->lists( 'migration' );
		foreach ( (array) $files as $f ) {
			$file = ROOT_PATH . '/system/database/migrations/' . $f . '.php';
			if ( is_file( $file ) ) {
				require $file;
				$class = 'system\database\migrations\\' . substr( basename( $file ), 18, - 4 );
				( new $class )->down();
				Db::table( 'migrations' )->where( 'migration', $f )->delete();
			}
		}
	}

	//迁移重置
	public function reset() {
		$files = Db::table( 'migrations' )->lists( 'migration' );
		foreach ( (array) $files as $f ) {
			$file = ROOT_PATH . '/system/database/migrations/' . $f . '.php';
			if ( is_file( $file ) ) {
				require $file;
				$class = 'system\database\migrations\\' . substr( basename( $file ), 18, - 4 );
				( new $class )->down();
			}
			Db::table( 'migrations' )->where( 'migration', $f )->delete();
		}
	}
}