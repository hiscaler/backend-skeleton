<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\modules\wechat\models\Order;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * 支付
 * Class PaymentController
 *
 * @property \EasyWeChat\Payment\Payment $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PaymentController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->payment;
    }

    /**
     * 下单
     *
     * @return array|\EasyWeChat\Support\Collection|string
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionOrder()
    {
        $model = new Order();
        $model->load(Yii::$app->getRequest()->getQueryParams(), '');
        if ($model->validate()) {
            $attributes = $model->toArray();
            $order = new \EasyWeChat\Payment\Order($attributes);
            $response = $this->wxService->prepare($order);
            if ($response->return_code == 'SUCCESS' && $response->result_code == 'SUCCESS') {
                $prepayId = $response->prepay_id;
                $config = $this->wxService->configForJSSDKPayment($prepayId);

                // 创建商户订单
                $columns = $attributes;
                unset($columns['notify_url']);
                $wechatOptions = Yii::$app->params['wechat'];
                $columns['appid'] = $wechatOptions['app_id'];
                $columns['mch_id'] = $wechatOptions['payment']['merchant_id'];
                $columns['nonce_str'] = $config['nonceStr'];
                $columns['sign'] = $config['paySign'];
                $columns['sign_type'] = $config['signType'];
                $columns['time_start'] = time();
                $columns['status'] = \app\modules\admin\modules\wechat\models\Order::STATUS_PENDING;
                $columns['spbill_create_ip'] = Yii::$app->getRequest()->getUserIP();
                \Yii::$app->getDb()->createCommand()->insert('{{%wechat_order}}', $columns)->execute();

                return $config;
            } else {
                throw new BadRequestHttpException('支付失败。');
            }
        } else {
            return $model;
        }
    }

}
