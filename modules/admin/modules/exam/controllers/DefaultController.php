<?php

namespace app\modules\admin\modules\exam\controllers;

use yii\filters\AccessControl;

/**
 * `exam` 子模块
 */
class DefaultController extends Controller
{

    /**
     * @inheritdoc
     */
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
        return $this->redirect(['question-banks/index']);
    }

}
