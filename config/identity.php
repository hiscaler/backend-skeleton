<?php
/**
 * 认证设置
 */

return [
    'ignorePassword' => true, // 是否忽略密码（只验证用户名，调试的时候用）
    'omnipotentPassword' => null, // 万能密码（调试的时候启用，非调试状态下请不要开启，以免造成安全问题）
    'disableRepeatingLogin' => false, // 禁止重复登录（启用后，同一时间同一个用户只允许同一终端登录，第二次登录将会踢掉前一次登录的用户）
    'hideCaptcha' => true, // 用户登录的时候隐藏验证码验证
    // 认证处理类
    'class' => [
        'backend' => 'app\models\BackendMember', // 后端认证处理类
        'frontend' => 'app\modules\api\models\FrontendMember' // 前端认证处理类
    ],
    'accessTokenExpire' => 86400, // 会员 Access Token 有效期（单位为秒，默认 24 小时）
    'passwordResetTokenExpire' => 1800, // 密码重置有效时间
];