**描述:**

PHP写的Mongodb操作数据库工具,类似mysql数据库操作软件Navicat,等等


**安装:**

1.新建composer.json文件

2.加入以下内容:

{
    "require": {
        "jiangshengxin/mongodb-tools":"dev-master"
    }
}


3.执行命令:

composer install

4.下载完毕以后,进入下载好的vendor/jiangshengxin.这个目录,就是类包目录.
进入这个类包目录,执行CMD命令,生成装载文件,就可以运行了:

composer update

**使用说明:**

demo.php是入口文件

**注意!**
 
这个操作需要php开启mongo扩展,不开扩展不可以用






