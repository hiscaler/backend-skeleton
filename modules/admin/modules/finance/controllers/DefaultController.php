<?php

namespace app\modules\admin\modules\finance\controllers;

use yii\filters\AccessControl;

/**
 * 默认控制器
 *
 * @package app\modules\admin\modules\finance\controllers
 * @author hiscaler <hiscaler@gmail.com>
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
