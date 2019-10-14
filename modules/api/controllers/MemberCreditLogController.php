<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\MemberCreditLog;
use app\modules\api\models\MemberCreditLogSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class MemberCreditLogController
 *
 * @package app\modules\api\controllers\
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberCreditLogController extends ActiveController
{

    public $modelClass = MemberCreditLog::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     * @throws \Throwable
     */
    public function prepareDataProvider()
    {
        $search = new MemberCreditLogSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}