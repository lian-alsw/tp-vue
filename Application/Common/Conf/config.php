<?php
/**[数据配置文件]
 * @Author: 250375742@qq.com
 * @Date:   2014-08-15 11:07:35
 * @Last Modified by:   Jason
 * @Last Modified time: 2015-05-04 09:17:38
 */
// 数据库连接信息=>数据库类型://用户名:密码@链接地址:密码/数据库名称
// auth权限设置
// AUTH_ON           认证开关
// AUTH_TYPE         认证方式，1为时时认证；2为登录认证。
// AUTH_GROUP        用户组数据表名
// AUTH_GROUP_ACCESS 用户组明细表
// AUTH_RULE         权限规则表
// AUTH_USER         用户信息表
return array (
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'db_dy',
    'DB_USER' => 'root',
    'DB_PWD' => '',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'db_',
    'DB_CHARSET' => 'utf8',
    //配置二级
    'MODULE_ALLOW_LIST'     =>  array('Home','Mobile','User','Admin','Iview'),// 允许访问的模块列表
    'APP_SUB_DOMAIN_DEPLOY' =>  true,   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  =>  array('www'=>'Home','m'=>'Mobile','user'=>'User','admin'=>'Admin','iview'=>'Iview','*' => 'Home'), // 子域名部署规则
    //'LOG_RECORD'=>true,
);
