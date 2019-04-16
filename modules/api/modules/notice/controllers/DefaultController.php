<?php

namespace app\modules\api\modules\notice\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\notice\models\Notice;
use app\modules\api\modules\notice\models\NoticeSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * /api/notice/default
 *
 * @package app\modules\api\modules\notice\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Notice::class;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['DELETE'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     * @throws \Throwable
     */
    public function prepareDataProvider()
    {
        $search = new NoticeSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}
