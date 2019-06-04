<?php
/**
 * @see https://www.easywechat.com/docs/3.x/zh-CN/configuration
 */
return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug' => true,

    /**
     * 是否激活微信第三方登录
     * 默认为不激活，激活后请在 thirdPartyLogin 项中配置 app_id 和 secret
     */
    'enableThirdPartyLogin' => false,

    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id' => 'your-app-id', // AppID
    'secret' => 'your-app-secret', // AppSecret
    'token' => 'your-token', // Token
    'aes_key' => '', // EncodingAESKey，安全模式与兼容模式下请一定要填写！！！
    'thirdPartyLogin' => [
        // 第三方登录
        'app_id' => '', // AppID
        'secret' => '', // AppSecret
    ],

    /**
     * 日志配置
     *
     * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
     * permission：日志文件权限（可选），默认为 null（若为 null 值，monolog 会取 0644）
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => 'debug',
        'permission' => 0777,
        'file' => \yii\helpers\FileHelper::normalizePath(__DIR__ . '/../runtime/logs/easy-wechat.log'),
    ],

    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址
     */
    'oauth' => [
        'scopes' => ['snsapi_userinfo'],
        'callback' => ['/api/wechat/oauth/callback'], // Yii URL route format
    ],

    /**
     * 微信支付
     */
    'payment' => [
        'merchant_id' => '', // 商户号
        'key' => '', // 支付密钥
        'cert_path' => 'certs/cert.pem', // 基于站点根目录
        'key_path' => 'certs/key',      // 基于站点根目录
        // 'device_info'     => '013467007045764',
        // 'sub_app_id'      => '',
        // 'sub_merchant_id' => '',
        // ...
    ],

    /**
     * Guzzle 全局设置
     *
     * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
     */
    'guzzle' => [
        'timeout' => 3.0, // 超时时间（秒）
        //'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
    ],

    // 其他配置
    'other' => [
        'subscribe' => [
            'required' => true, // 是否必须关注，如果未关注的话跳转到关注提醒页面
            'redirectUrl' => '', // 关注页面地址
            'deleteAfterCancel' => false, // 取消关注后是否删除微信记录
        ],
        // 菜单
        'menu' => [
            'buttons' => [
                [
                    "type" => "view",
                    "name" => "test",
                    'http://www.example.com'
                ],
            ],
            'matchRule' => []
        ],
        'oauth' => [
            'autoRegister' => false, // 第三方扫码后如果不是新会员是否自动注册
            'appendValueIfNotExistsMember' => 'type=register', // 扫码后会员不存在的情况下在跳转的页面地址上添加什么参数
            'appendValueIfExistsMember' => 'type=bind', // 扫码后会员存在的情况下在跳转的页面地址上添加什么参数
        ]
    ],
    // 微信支付发起回调后的业务逻辑处理类
    'business' => [
        'class' => \app\modules\api\modules\wechat\business\RechargeBusiness::class,
    ]
];
