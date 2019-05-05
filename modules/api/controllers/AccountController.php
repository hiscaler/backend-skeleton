<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\Member;
use app\modules\api\models\MemberProfile;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * 帐号管理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class AccountController extends ActiveController
{

    public $modelClass = Member::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['create'], $actions['delete'], $actions['update'], $actions['view']);

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
                    'update' => ['PUT', 'PATCH'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['update', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * 更新
     *
     * @return Member|MemberProfile
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionUpdate()
    {
        $model = $this->findModel();
        $payload = Yii::$app->getRequest()->getBodyParams();
        !isset($payload['profile']) && $payload['profile'] = [];
        /* @var $profileModel MemberProfile */
        if ($model->profile) {
            $profileModel = $model->profile;
        } else {
            $payload['profile']['member_id'] = $model->id;
            $profileModel = new MemberProfile();
        }
        if ($model->load($payload, '')
            && $profileModel->load($payload, 'profile')
            && $model->validate()
            && $profileModel->validate()
        ) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            try {
                $model->save();
                $model->saveProfile($profileModel);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } elseif (!$model->hasErrors() && !$profileModel->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        if ($model->hasErrors()) {
            return $model;
        }
        if ($profileModel->hasErrors()) {
            return $profileModel;
        }

        return $model;
    }

    /**
     * 帐号详情
     *
     * @return Member|null
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        return $this->findModel();
    }

    /**
     * @return Member|null
     * @throws NotFoundHttpException
     */
    protected function findModel()
    {
        $model = Member::findOne(\Yii::$app->getUser()->getId());
        if ($model === null) {
            throw new NotFoundHttpException('Not found');
        }

        return $model;
    }

}