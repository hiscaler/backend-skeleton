<?php

namespace app\modules\admin\modules\ticket\controllers;

use yii\filters\AccessControl;

/**
 * `ticket` 子模块
 */
class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['tickets/index']);
    }

}
