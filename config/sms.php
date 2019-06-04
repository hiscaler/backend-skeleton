<?php

return [
    'private' => [
        /**
         * 短信发送内容处理设置
         * 格式：key => value 键值对格式，其中 key 只能使用小写的英文字母，不能包含数字或者其他特殊字符，value 为业务逻辑处理类名称
         */
        'business' => [
            'captcha' => \app\business\SmsCaptchaBusiness::class
        ],
    ],
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
        // 默认可用的发送网关
        'gateways' => [
            'aliyun',
        ],
    ],
    /**
     * 可用的网关配置
     * 如果涉及到模板 id，请设置 “_templateId” 值
     */
    'gateways' => [
        'errorlog' => [
            'file' => \yii\helpers\FileHelper::normalizePath(__DIR__ . '/../runtime/logs/easy-sms.log'),
        ],
        'aliyun' => [
            'access_key_id' => '',
            'access_key_secret' => '',
            'sign_name' => '',
            '_templateId' => ''
        ],
        //...
    ],
];