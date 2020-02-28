<?php
/**
 * 会员设置
 */
return [
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
        'usable_scope' => 1, // 会员使用范围（0: 全部, 1: 前台, 2: 后台）
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
];