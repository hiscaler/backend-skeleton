<?php

namespace app\modules\api\extensions;

use app\modules\api\extensions\yii\filters\auth\AccessTokenAuth;

/**
 * Class AuthController
 *
 * @package app\modules\api\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class AuthController extends BaseController
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => AccessTokenAuth::class,
            ]
        ]);
    }

}