<?php

namespace app\modules\api\modules\wechat\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * 模板消息
 * Class NoticeController
 *
 * @property \EasyWeChat\Notice\Notice $service
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class NoticeController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->service = $this->_application->notice;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'send' => ['post'],
            ]
        ];

        return $behaviors;
    }

    /**
     * 发送模板消息
     *
     * @return \EasyWeChat\Support\Collection
     * @throws BadRequestHttpException
     */
    public function actionSend()
    {
        $request = Yii::$app->getRequest();
        $templateId = trim($request->post('templateId'));
        $openId = trim($request->post('openId'));
        $url = trim($request->post('url'));
        $rawData = $request->post('data');

        if ($templateId && $openId && $rawData) {
            try {
                $data = Json::decode($rawData);
                if ($data === null) {
                    throw new BadRequestHttpException('无效的 data 参数。');
                }
                $messageId = $this->service->send([
                    'touser' => trim($openId),
                    'template_id' => $templateId,
                    'url' => $url ? urldecode($url) : '',
                    'data' => $data,
                ]);
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);

                return $messageId;
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        } else {
            throw new BadRequestHttpException("参数有误。请提供 templateId, openId, data 参数");
        }
    }

}
