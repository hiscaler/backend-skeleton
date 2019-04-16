<?php

namespace app\modules\admin\modules\finance\controllers;

use yii\filters\AccessControl;

/**
 * `finance` 子模块
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
        return $this->redirect(['finances/index']);
    }

}
