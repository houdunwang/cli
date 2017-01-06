<?php namespace system\database\migrations;
use houdunwang\database\build\Migration;
use houdunwang\database\build\Blueprint;

class {{className}} extends Migration {
    //执行
	public function up() {
		Schema::table( '{{TABLE}}', function ( Blueprint $table ) {
			//$table->string('name', 50)->change();
        });
    }

    //回滚
    public function down() {

    }
}