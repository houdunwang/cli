# 命令行操作组件

使用cli组件用于操作命令行动作

登录 [GITHUB](https://github.com/houdunwang/cli)  查看源代码

[TOC]
#开始使用

####安装组件
使用 composer 命令进行安装或下载源代码使用。

```
composer require houdunwang/cli
```
> HDPHP 框架已经内置此组件，无需要安装

####创建对象
```
$obj = new \houdunwang\cli\Cli();
```

####绑定命令
绑定命令使用闭包进行操作,闭包中的第一个参数为Cli组件实例。
```
$obj->bind( 'hd', function ( $cli, $arg1, $arg2 ) {
	echo $arg1, $arg2;
	//正确提示信息
	$cli->success( '操作成功' );
	
	//错误提示信息
    $cli->error( '执行失败' );
});
```
