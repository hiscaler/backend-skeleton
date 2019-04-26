<?php

namespace app\modules\api\modules\ticket\controllers;

use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\modules\ticket\models\Ticket;
use app\modules\api\modules\ticket\models\TicketMessageSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * `ticket/message` 工单消息接口
 *
 * @package app\modules\api\modules\ticket\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MessageController extends Controller
{

    public $modelClass = Ticket::class;

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
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['DELETE'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
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
        $search = new TicketMessageSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}
