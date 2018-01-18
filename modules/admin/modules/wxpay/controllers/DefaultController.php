<?php

namespace app\modules\admin\modules\wxpay\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default controller for the `wxpay` module
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
                'class' => AccessControl::className(),
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
