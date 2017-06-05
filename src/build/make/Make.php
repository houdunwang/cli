<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDPHP framework]
 * |      Site: www.hdphp.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.hdphp.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/

namespace houdunwang\cli\build\make;

use houdunwang\cli\build\Base;
use houdunwang\dir\Dir;
use houdunwang\loader\Loader;

class Make extends Base
{
    /**
     * 创建控制器
     *
     * @param        $arg
     * @param string $type
     */
    public function controller($arg, $type = 'controller')
    {
        $info       = explode('.', $arg);
        $MODULE     = $info[0];
        $CONTROLLER = ucfirst($info[1]);
        $file       = self::$path['controller'].'/'.$MODULE.'/controller/'
                      .ucfirst($CONTROLLER).'.php';;
        if ( ! Dir::create(dirname($file))) {
            $this->error("Directory to create failure");
        }
        //创建文件
        if (is_file($file)) {
            $this->error('Controller file already exists');
        }
        $data = file_get_contents(__DIR__.'/view/'.strtolower($type).'.tpl');
        $data = str_replace(
            ['{{APP}}', '{{MODULE}}', '{{CONTROLLER}}'],
            [
                c('app.path'),
                $MODULE,
                $CONTROLLER,
            ],
            $data
        );
        file_put_contents($file, $data);
        $this->success('Controller creating successful');
    }

    //创建模型
    public function model($arg)
    {
        Dir::create(self::$path['model']);
        //命名空间
        $namespace = str_replace('/', '\\', self::$path['model']);
        $info      = explode('.', $arg);
        $MODEL     = ucfirst($info[0]);
        $TABLE     = strtolower($info[0]);
        $file      = self::$path['model'].'/'.ucfirst($MODEL).'.php';
        //创建模型文件
        if (is_file($file)) {
            $this->error("Model file already exists");
        }
        $data = file_get_contents(__DIR__.'/view/model.tpl');
        $data = str_replace(
            ['{{NAMESPACE}}', '{{MODEL}}', '{{TABLE}}'],
            [$namespace, $MODEL, $TABLE],
            $data
        );
        file_put_contents($file, $data);
    }

    //创建数据迁移
    public function migration($name, $arg)
    {
        Dir::create(self::$path['migration']);
        $info = explode('=', $arg);
        //检查数据迁移文件是否已经存在
        $files = glob(self::$path['migration'].'/*.php');
        foreach ((array)$files as $file) {
            if (stristr($file, $name)) {
                $this->error('File already exists');
            }
        }
        $file = self::$path['migration'].'/'.date('ymdhis').'_'.$name.'.php';
        //命名空间
        $namespace = str_replace('/', '\\', self::$path['migration']);
        //新增表
        if ($info[0] == '--create') {
            //创建模型文件
            $data = file_get_contents(
                __DIR__.'/view/migration.create.tpl'
            );
            $data = str_replace(
                ['{{NAMESPACE}}', '{{TABLE}}', '{{className}}'],
                [$namespace, $info[1], $name],
                $data
            );
            file_put_contents($file, $data);
        }
        //修改表
        if ($info[0] == '--table') {
            $data = file_get_contents(__DIR__.'/view/migration.table.tpl');
            $data = str_replace(
                ['{{NAMESPACE}}', '{{TABLE}}', '{{className}}'],
                [$namespace, $info[1], $name],
                $data
            );

            return file_put_contents($file, $data);
        }
    }

    //创建数据迁移
    public function seed($name)
    {
        Dir::create(self::$path['seed']);
        //检测文件是否存在,也检测类名
        $files = glob(self::$path['seed'].'/*.php');
        //命名空间
        $namespace = str_replace('/', '\\', self::$path['seed']);
        foreach ((array)$files as $file) {
            $fileInfo = pathinfo($file);
            $basename = strtolower(
                substr($fileInfo['basename'], 0, strlen($name))
            );
            if ($basename == strtolower($name)) {
                $this->error('File already exists');
            }
        }

        $file = self::$path['seed'].'/'.date('ymdhis').'_'.$name.'.php';
        //创建文件
        $data = file_get_contents(__DIR__.'/view/seeder.tpl');
        $data = str_replace(
            ['{{NAMESPACE}}', '{{className}}'],
            [$namespace, $name],
            $data
        );

        return file_put_contents($file, $data);
    }

    //创建标签
    public function tag($name)
    {
        $file = self::$path['tag'].'/'.ucfirst($name).'.php';
        if (is_file($file)) {
            $this->error('File already exists');
        }
        //创建文件
        $data = file_get_contents(__DIR__.'/view/tag.tpl');
        $data = str_replace(['{{NAME}}'], [ucfirst($name)], $data);
        file_put_contents($file, $data);
    }

    //创建中间件
    public function middleware($name)
    {
        $file = self::$path['middleware'].'/'.ucfirst($name).'.php';
        if (is_file($file)) {
            $this->error('File already exists');
        }
        //创建文件
        $data = file_get_contents(__DIR__.'/view/middleware.tpl');
        $data = str_replace(['{{NAME}}'], [ucfirst($name)], $data);
        file_put_contents($file, $data);
    }

    //创建应用密钥
    public function key()
    {
        $key     = md5(mt_rand(1, 99999).time()).md5(mt_rand(1, 99999).time());
        $content = file_get_contents('system/config/app.php');
        $content = preg_replace(
            '/(.*("|\')\s*key\s*\2\s*=>\s*)(.*)/im',
            "\\1'$key',",
            $content
        );
        file_put_contents('system/config/app.php', $content);
    }

    //创建服务
    public function service($name)
    {
        $name  = ucfirst($name);
        $files = [
            __DIR__.'/view/service/HdForm.tpl',
            __DIR__.'/view/service/HdFormFacade.tpl',
            __DIR__.'/view/service/HdFormProvider.tpl',
        ];
        //创建目录
        $dir = strtolower(self::$path['service'].'/'.$name);
        Dir::create($dir);
        foreach ($files as $f) {
            $content = str_replace(
                '{{LOWER_NAME}}',
                strtolower($name),
                file_get_contents($f)
            );
            $content = str_replace('{{NAME}}', $name, $content);
            if (strpos($f, 'Facade') !== false) {
                file_put_contents($dir."/{$name}Facade.php", $content);
            } else if (strpos($f, 'Provider') !== false) {
                file_put_contents($dir."/{$name}Provider.php", $content);
            } else {
                file_put_contents($dir."/{$name}.php", $content);
            }
        }
    }

    /**
     * 创建单元测试文件
     *
     * @param  string $name 类名称
     * @param string  $type 测试类型
     */
    public function test($name, $type = '--feature')
    {
        switch ($type) {
            case '--feature':
                $file = self::$path['test'].'/feature/'.$name.'.php';
                break;
            case '--unit':
                $file = self::$path['test'].'/unit/'.$name.'.php';
                break;
            default:
                $this->error('params is wrong');
        }
        if (is_file($file)) {
            $this->error('Files is Exists');
        }
        $content = file_get_contents(__DIR__.'/view/test.tpl');
        $content = str_replace(
            ['{{MODE}}', '{{NAME}}'],
            [trim($type, '--'), $name],
            $content
        );
        file_put_contents($file, $content);
        $this->success('File is Create Success');
    }

    public function request($name)
    {
        $file = self::$path['request'].'/'.$name.'.php';
        if (is_file($file)) {
            $this->error('Files is Exists');
        }
        $content = file_get_contents(__DIR__.'/view/request.tpl');
        $content = str_replace(
            ['{{NAME}}'],
            [$name],
            $content
        );
        file_put_contents($file, $content);
        $this->success('File is Create Success');
    }
}