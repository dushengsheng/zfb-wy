<?php

namespace Config;

/**
 * mysql配置
 * @author walkor
 */
class Db {

    /**
     * 数据库的一个实例配置，则使用时像下面这样使用
     * $user_array = Db::instance(user)->select(name,age)->from(users)->where(age>12)->query();
     * 等价于
     * $user_array = Db::instance(user)->query(SELECT `name`,`age` FROM `users` WHERE `age`>12);
     * @var array
     */
    public static $db = array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'xianyu',
        'password' => 'woaini520DSS@wepay',
        'dbname' => 'xianyu',
        'charset' => 'utf8',
    );

}
