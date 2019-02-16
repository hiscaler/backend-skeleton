<?php

namespace app\modules\api\modules\wechat\controllers;

use Yii;
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

    /**
     * 发送
     *
     * @param $data
     * @return \EasyWeChat\Support\Collection
     * @throws BadRequestHttpException
     */
    public function actionSend($data)
    {
        try {
            $data = json_decode($data, true);
            $messageId = $this->service->send($data);
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);

            return $messageId;
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

}
