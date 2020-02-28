<?php

namespace app\helpers;

use Yii;

/**
 * Class App
 *
 * @package app\helpers
 * @author hiscaler <hiscaler@gmail.com>
 */
class App
{

    /**
     * 获取模拟会员 access_token 值，用于后台 api 请求
     *
     * @return false|string|null
     * @throws \yii\db\Exception
     */
    public static function getFakeMemberAccessToken()
    {
        $token = null;
        $username = trim(Config::get('user.fakeMember'));
        if ($username) {
            $token = Yii::$app->getDb()
                ->createCommand('SELECT [[access_token]] FROM {{%member}} WHERE [[username]] = :username', [':username' => $username])
                ->queryScalar();
            $token = $token !== false ? $token : null;
        }

        return $token;
    }

    /**
     * 是否启用了 RBAC
     *
     * @return bool
     */
    public static function rbacWorking()
    {
        return Config::get('rbac.debug') === false && Yii::$app->getAuthManager();
    }

}