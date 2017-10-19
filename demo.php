<?php
header("content-type:text/html;charset=utf-8");

require_once __DIR__.'/vendor/autoload.php';
use MongodbTools\Rock;

/**
 * Defining version number and enabling error reporting
 */
define("ROCK_MONGO_VERSION", "1.1.8");

error_reporting(E_ALL);

/**
 * Environment detection
 */
if (!version_compare(PHP_VERSION, "5.0")) {
    exit("为了使事情正确，您必须安装PHP5");
}
if (!class_exists("Mongo") && !class_exists("MongoClient")) {
    exit("为了使事情正确，必须安装phpmongo模块. <a href=\"http://www.php.net/manual/en/mongo.installation.php\" target=\"_blank\">这里是php.net上的安装文档.</a>");
}

// enforce Mongo support for int64 data type (Kyryl Bilokurov <kyryl.bilokurov@gmail.com>)
if (PHP_INT_SIZE == 8) {
    ini_set("mongo.native_long", 1);
    ini_set("mongo.long_as_object", 1);
}

/**
 * Initializing configuration files and RockMongo
 */
require "src/config.php";
define("DS", DIRECTORY_SEPARATOR);
define("__ROOT__", dirname(__FILE__) . DS . "src/app");
define("__VERSION__", "0.0.1");
define("nil", "nil_" . uniqid(microtime(true)));
if (!defined("__ENV__")) {
    define("__ENV__", "dev");
}
if (!defined("__PLATFORM__")) {
    define("__PLATFORM__", "def");
}
if (!isset($_SERVER["PHP_SELF"]) && isset($_SERVER["SCRIPT_NAME"])) {
    $_SERVER["PHP_SELF"] = $_SERVER["SCRIPT_NAME"];
}

//merge $_POST and $_GET
$GLOBALS["ROCK_USER_VARS"] = array();
$GLOBALS["ROCK_HTTP_VARS"] = array_merge($_GET, $_POST);

require_once "src/function.php";
rock_check_version();
rock_init_lang();
rock_init_plugins();
Rock::start();



/**
 * demo.php
 *
 * 入口文件
 *
 * 2017 Copyright (c) http://note.hanfu8.top
 *
 * 修改历史
 * ----------------------------------------
 * 2017/10/19, 作者:降省心(QQ:1348550820), 操作:创建
 *
 * 2017-10-19 18:40:16 作者:降省心 操作:配置文件在src/config.php
 *
 * 2017-10-19 18:40:36 作者:降省心 操作:修复已知bug
 **/