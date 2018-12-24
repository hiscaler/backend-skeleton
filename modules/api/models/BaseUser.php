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
            'avatar' => function () {
                $avatar = $this->avatar;
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
            'email',
            'role',
            'registerIp' => 'register_ip',
            'loginCount' => 'login_count',
            'lastLoginIp' => 'last_login_ip',
            'lastLoginTime' => 'last_login_time',
            'lastLoginSession' => 'last_login_session',
            'status',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }

}