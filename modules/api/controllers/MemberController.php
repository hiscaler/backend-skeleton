<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\Member;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * api/member/ 接口
 * Class MemberController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberController extends ActiveController
{

    public $modelClass = Member::class;

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

}