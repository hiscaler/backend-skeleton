<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\forms\SmsForm;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * 短信发送
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class SmsController extends BaseController
{

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * 发送短信
     *
     * @rbacDescription 发送短信
     * @return SmsForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSend()
    {
        $model = new SmsForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate() && $model->send()) {
            Yii::$app->getResponse()->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

}