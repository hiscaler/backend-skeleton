<?php

namespace app\modules\admin\modules\wechat\controllers;

use app\modules\admin\extensions\BaseController;
use yii\filters\AccessControl;

/**
 * Default controller for the `wechat` module
 */
class DefaultController extends BaseController
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
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->redirect(['orders/index']);
    }
}
