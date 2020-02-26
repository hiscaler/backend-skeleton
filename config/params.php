<?php

return [
    'adminEmail' => 'admin@example.com',
    'user.passwordResetTokenExpire' => 1800, // 密码重置有效时间
    'member.accessTokenExpire' => 86400, // 会员 Access Token 有效期（单位为秒，默认 24 小时）
    // 系统用户
    'user' => [
        'fakeMember' => 'tmp', // 后台发起 api 请求模拟的用户
    ],
    // 认证处理
    'identityClass' => [
        'backend' => 'app\models\BackendMember', // 后端认证处理类
        'frontend' => 'app\modules\api\models\FrontendMember' // 前端认证处理类
    ],
    // 会员
    'member' => [
        /**
         * 会员类型选项
         * 格式：key => value
         * key 为除 1 外的正整数, 1 表示系统管理员
         * value 可以为任意字符串
         */
        'types' => [],
        'register' => [
            'type' => 'wx', // 注册类型 normal: 常规表单注册, wx: 使用微信第三方注册
            'status' => 1, // 会员注册默认值（\app\models\Member::STATUS_ACTIVE）
            'expiryMinutes' => 0, // 有效截止时间（单位为：分钟），如果为 0 表示无限制
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
        // 自定义积分消费选项
        'creditOperations' => [
            'task' => '任务消费',
        ],
        /**
         * 设定会员需要处理的业务逻辑，支持多个。
         * 设定的方式采用 key => value 方式，key 为类定义，value 则为业务逻辑处理中需要的相关数据，如果没有，您可以设置为空数组
         *
         * 建议将您需要处理的逻辑拆分为最小颗粒度，即保持一个类只处理一个相关的业务，这样有利于您的业务组合。类中涉及到数据库处理的部分，系统会自动维护事务，无须自行处理。
         * 比如：会员注册后需要进行两方面的数据处理，第一：送给新用户 100 积分，第二：如果会员填写有推荐码，则赠送给推荐人 50 积分。
         * 在这种情况下，我们需要将第一，第二两个业务逻辑处理分开编写和设定
         * [
         *     \app\business\FirstClass::class => [],
         *     \app\business\SecondClass::class => [],
         * ]
         * 这样的设定有几个好处：
         * 第一：我们只需要关注特定的业务逻辑处理
         * 第二：当某个业务逻辑处理不需要的时候，我们可以简单的去掉设定来达到我们的业务要求
         */
        'business' => [
            \app\business\MemberNothingBusiness::class => [],
        ]
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
    'upload' => [
        'dir' => 'uploads', // 文件保存目录（相对于根目录而言，请不要填写绝对路径）
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
            'extensions' => 'zip,rar,7z,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,wps'
        ]
    ],
    'rbac' => require(__DIR__ . '/rbac.php'),  // 权限认证设置
    'wechat' => require(__DIR__ . '/wechat.php'),  // 微信公众号设置
    'module' => require(__DIR__ . '/module.php'), // 模块设置
    'sms' => require(__DIR__ . '/sms.php'), // 短信设置
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
                'forceEmbed' => false,
            ],
            'app-models-Lookup' => [
                'id' => 'lookups',
                'label' => 'Lookups',
                'url' => ['lookups/form'],
                'forceEmbed' => false,
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
                'forceEmbed' => false,
            ],
            'app-models-FileUploadConfig' => [
                'id' => 'file-upload-config',
                'label' => 'File Upload Configs',
                'url' => ['file-upload-configs/index'],
                'forceEmbed' => false,
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
            'app-models-MemberLoginLog' => [
                'id' => 'member-login-log',
                'label' => 'Member Login Logs',
                'url' => ['member-login-logs/index'],
                'forceEmbed' => true,
            ],
            'db' => [
                'id' => 'db',
                'label' => 'DB',
                'url' => ['db/index'],
                'forceEmbed' => false,
            ],
            'cache' => [
                'id' => 'cache',
                'label' => 'Cache',
                'url' => ['caches/index'],
                'forceEmbed' => true,
            ],
        ],
    ],
];
