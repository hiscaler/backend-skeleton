<?php
/**
 * 权限认证设置
 */
return [
    'debug' => false, // 是否开启调试模式（调试模式下不启用权限认证）
    'ignoreUsers' => [], // 启用权限认证的情况下这些用户名登录的用户不受控制，可以使用全部的权限，方便调试。
    'userTable' => [
        'name' => '{{%member}}', // 查询的用户表
        'columns' => [
            'id' => 'id', // 用户的唯一主键
            'username' => 'username', // 用户名
            /**
             * 扩展字段（数据库字段名称 => 显示名称）
             *
             * 'extra' => [
             *     'nickname' => '昵称',
             *     'email' => '邮箱',
             * ]
             */
            'extra' => [
                'nickname' => '昵称',
                'role' => '角色',
            ],
        ],
        'where' => [], // 查询条件
    ],
    'disabledScanModules' => ['gii', 'debug'], // 禁止扫描的模块
    // 以下地址的将忽略
    'ignorePermissionNames' => [
        'admin-default.login',
        'admin-default.logout',
        'admin-default.error',
        'admin-default.index',
        'admin-default.captcha',
        'admin-account.index',
        'admin-account.change-password',
        'admin-account.login-logs',
        'api-default.index',
    ],
    'selfish' => true, // 是否只显示当前应用的相关数据
];