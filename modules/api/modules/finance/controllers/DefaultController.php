<?php

namespace app\modules\api\modules\finance\controllers;

use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\modules\finance\models\Finance;
use app\modules\api\modules\finance\models\FinanceSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * `finance/default` 财务接口
 *
 * @package app\modules\api\modules\finance\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = Finance::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['create']['class'] = CreateAction::class;

        return $actions;
    }

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
     */
    public function prepareDataProvider()
    {
        $search = new FinanceSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}
