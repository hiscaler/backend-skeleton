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
 * Class AccountController
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
        unset($actions['index'], $actions['create'], $actions['delete']);

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
     * @param $id
     * @return Member|MemberProfile
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /* @var $profileModel MemberProfile */
        $profileModel = $model->profile ?: new MemberProfile();

        $payload = Yii::$app->getRequest()->getBodyParams();
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
     * @param $id
     * @return Member|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Member::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('Not found');
        }

        return $model;
    }

}