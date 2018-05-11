<?php

namespace app\modules\admin\modules\miniActivity\controllers;

use yii\filters\AccessControl;

/**
 * 微活动管理
 *
 * @author hiscaler <hiscaler@gmail.com>
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

    public function actionIndex()
    {
        $this->redirect(['wheels/index']);
    }

}
