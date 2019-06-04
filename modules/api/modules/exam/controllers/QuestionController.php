<?php

namespace app\modules\api\modules\exam\controllers;

use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\modules\exam\models\QuestionBank;
use app\modules\api\modules\exam\models\QuestionSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * `exam/question` 接口
 *
 * @package app\modules\api\modules\exam\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class QuestionController extends Controller
{

    public $modelClass = QuestionBank::class;

    public function actions()
    {
        $actions = parent::actions();
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
                        'actions' => ['index', 'create', 'view', 'delete', 'update'],
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
        $search = new QuestionSearch();

        return $search->search(Yii::$app->getRequest()->getQueryParams());
    }

}
