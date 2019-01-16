<?php

namespace app\modules\api\models;

use Yii;

/**
 * Class BaseUser
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseUser extends \app\models\User
{

    public function fields()
    {
        return [
            'id',
            'username',
            'nickname',
            'avatar' => function ($model) {
                $avatar = $model->avatar;
                if (!empty($avatar)) {
                    $addUrl = true;
                    foreach (['http', 'https', '//'] as $prefix) {
                        if (strncasecmp($avatar, $prefix, strlen($prefix)) === 0) {
                            $addUrl = false;
                            break;
                        }
                    }

                    if ($addUrl) {
                        $avatar = Yii::$app->getRequest()->hostInfo . $avatar;
                    }
                }

                return $avatar;
            },
            'access_token',
            'email',
            'role',
            'register_ip',
            'login_count',
            'last_login_ip',
            'last_login_time',
            'last_login_session',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}