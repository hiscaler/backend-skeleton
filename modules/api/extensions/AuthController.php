<?php

namespace app\modules\api\extensions;

use app\modules\api\extensions\yii\filters\auth\AccessTokenAuth;
use yii\filters\auth\QueryParamAuth;

class AuthController extends BaseController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $token = \Yii::$app->getRequest()->get('accessToken');
        if (empty($token)) {
            $headers = \Yii::$app->getRequest()->getHeaders();
            $token = $headers->has('accessToken') ? $headers->get('accessToken') : null;
        }
        if (!empty($token)) {
            $class = AccessTokenAuth::class;
        } else {
            $class = QueryParamAuth::class;
        }

        $behaviors = array_merge($behaviors, [
            'authenticator' => [
                'class' => $class,
            ]
        ]);

        return $behaviors;
    }
}