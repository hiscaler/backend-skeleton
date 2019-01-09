<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\MemberProfile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class MemberProfileController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberProfileController extends ActiveController
{

    public $modelClass = MemberProfile::class;

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