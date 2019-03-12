<?php

return [
    'adminEmail' => 'admin@example.com',
    'user.passwordResetTokenExpire' => 1800, // 密码重置有效时间
    'member.accessTokenExpire' => 86400, // 会员 Access Token 有效期（单位为秒，默认 24 小时）
    // 会员
    'member' => [
        'register' => [
            'type' => 'wx', // 注册类型 normal: 常规表单注册, wx: 使用微信第三方注册
            'status' => 0, // 会员注册默认值（待审核）
            'expiryMinutes' => 10, // 有效截止时间（单位为：分钟），如果为 0 表示无限制
            'rules' => [
                'required' => ['mobile_phone'], // 必填的字段
                'unique' => ['mobile_phone'], // 保持数据唯一性的字段
            ]
        ],
        'login' => [
            /**
             * 会员到期后如何处理，continue 表示可以继续登录操作，其他字符串则表示禁止登录
             * 此配置主要用于某些情况下会员到期，是可以继续登录系统的，只是系统中的相关操作会做进一步的限制。
             */
            'expiredAfter' => 'continue',
        ],
    ],
    // 接口设置
    'api' => [
        'dbCacheDuration' => 300, // 是否激活 API 数据库查询缓存，默认 5 分钟（以秒为单位），如果设置为 null 则表示不启用缓存，
        'user' => [
            'identityClass' => \app\modules\api\models\Member::class,
        ],
    ],
    'uninstall.module.after.droptable' => false,// 卸载模块后是否同步删除模块相关表
    'ignorePassword' => true, // 是否忽略密码（只验证用户名，调试的时候用）
    'omnipotentPassword' => null, // 万能密码（调试的时候启用，非调试状态下请不要开启，以免造成安全问题）
    'disableRepeatingLogin' => false, // 禁止重复登录（启用后，同一时间同一个用户只允许同一终端登录，第二次登录将会踢掉前一次登录的用户）
    'hideCaptcha' => true, // 用户登录的时候隐藏验证码验证
    'fromMailAddress' => [
        'admin@example.com' => 'you name',
    ],
    // 文件上传设置
    'uploading' => [
        'path' => 'uploads',
        // 请参考 \yii\web\ImageValidator 类属性进行设置
        'image' => [
            'minSize' => 1024,
            'maxSize' => 1024 * 1024 * 200,
            'extensions' => 'png,gif,jpg,jpeg'
        ],
        // 请参考 \yii\web\FileValidator 类属性进行设置
        'file' => [
            'minSize' => 1024,
            'maxSize' => 1024 * 1024 * 200,
            'extensions' => 'zip,rar,pdf,doc,docx,xls,xlsx,ppt,pptx,wps,7z,txt'
        ]
    ],
    // 权限认证设置
    'rbac' => [
        'debug' => false, // 是否调试模式(调试模式下不启用权限认证)
        'ignoreUsers' => ['admin'], // 启用权限认证的情况下这些用户名登录的用户不受控制，可以使用全部的权限，方便调试。
        'userTable' => [
            'name' => '{{%user}}', // 查询的用户表
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
        'disabledScanModules' => ['gii', 'debug', 'api'], // 禁止扫描的模块
        'selfish' => true, // 是否只显示当前应用的相关数据
    ],
    // 微信公众号设置
    'wechat' => require(__DIR__ . '/wechat.php'),
    'modules' => [
        /**
         *'app-models-Article' => [
         *    'id' => 'articles', // 控制器名称（唯一）
         *    'label' => 'Articles', //  需要翻译的文本（app.php）
         *    'url' => ['/articles/index'], // 访问 URL
         *    'activeConditions' => [], // 激活条件，填写控制器 id
         *    'forceEmbed' => true, // 是否强制显示在控制面板中
         * ],
         */
        'System Manage' => [
            'app-models-User' => [
                'id' => 'users',
                'label' => 'Users',
                'url' => ['users/index'],
                'forceEmbed' => true,
            ],
            'app-models-Module' => [
                'id' => 'modules',
                'label' => 'Modules',
                'url' => ['modules/index'],
                'forceEmbed' => true,
            ],
            'app-models-meta' => [
                'id' => 'meta',
                'label' => 'Meta',
                'url' => ['meta/index'],
                'forceEmbed' => true,
            ],
            'app-models-Lookup' => [
                'id' => 'lookups',
                'label' => 'Lookups',
                'url' => ['lookups/form'],
                'forceEmbed' => true,
            ],
            'app-models-Category' => [
                'id' => 'categories',
                'label' => 'Categories',
                'url' => ['categories/index'],
                'forceEmbed' => true,
            ],
            'app-models-Label' => [
                'id' => 'labels',
                'label' => 'Labels',
                'url' => ['labels/index'],
                'forceEmbed' => true,
            ],
            'app-models-FileUploadConfig' => [
                'id' => 'file-upload-config',
                'label' => 'File Upload Configs',
                'url' => ['file-upload-configs/index'],
                'forceEmbed' => true,
            ],
            'app-models-MemberGroup' => [
                'id' => 'member-group',
                'label' => 'Member Groups',
                'url' => ['member-groups/index'],
                'forceEmbed' => false,
            ],
            'app-models-Member' => [
                'id' => 'member',
                'label' => 'Members',
                'url' => ['members/index'],
                'forceEmbed' => true,
            ],
            'db' => [
                'id' => 'db',
                'label' => 'DB',
                'url' => ['db/index'],
                'forceEmbed' => true,
            ],
        ],
    ],
];
