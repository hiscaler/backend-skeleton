<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\modules\wechat\models\Order;
use EasyWeChat\Payment\API;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

    private $_notify_url;

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->payment;
        $notifyUrl = isset($this->wxConfig['payment']['notify_url']) ? $this->wxConfig['payment']['notify_url'] : null;
        if (empty($notifyUrl)) {
            $this->_notify_url = Url::toRoute(['/api/payment/notify'], true);
        } elseif (is_array($notifyUrl)) {
            $this->_notify_url = Url::toRoute($notifyUrl, true);
        } else {
            $this->_notify_url = (string) $notifyUrl;
        }
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
        $model->notify_url = $this->_notify_url;
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

    /**
     * 支付回调通知
     *
     * @throws \yii\base\ExitException
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function actionNotify()
    {
        $response = $this->wxService->handleNotify(function ($notify, $successful) {
            if ($successful) {
                $db = \Yii::$app->getDb();
                $orderId = $db->createCommand('SELECT [[id]] FROM {{%wechat_order}} WHERE [[appid]] = :appId AND [[nonce_str]] = :nonceStr AND [[out_trade_no]] = :outTradeNo AND [[openid]] = :openid', [':appId' => $notify['appid'], ':nonceStr' => $notify['nonce_str'], ':outTradeNo' => $notify['out_trade_no'], ':openid' => $notify['openid']])->queryScalar();
                if ($orderId) {
                    $columns = [
                        'transaction_id' => $notify['transaction_id'],
                        'time_expire' => $notify['time_end'],
                    ];
                    if (isset($notify['trade_state'])) {
                        $columns['trade_state'] = $notify['trade_state'];
                        $columns['trade_state_desc'] = $notify['trade_state_desc'];
                    }
                    $db->createCommand()->update('{{%wechat_order}}', $columns, ['id' => $orderId])->execute();

                    return true;
                } else {
                    throw new NotFoundHttpException('ORDER NOT FOUND');
                }
            } else {
                return false;
            }
        });

        Yii::$app->getResponse()->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->content = ($response->getContent());
        Yii::$app->end();
    }

    /**
     * 撤销订单
     *
     * @param $sn
     * @param string $type
     * @return \EasyWeChat\Support\Collection
     * @throws BadRequestHttpException
     */
    public function actionReverse($sn, $type = API::OUT_TRADE_NO)
    {
        $types = [
            API::TRANSACTION_ID => 'transaction_id',
            API::OUT_TRADE_NO => 'out_trade_no',
        ];
        if (isset($types[$type])) {
            throw new BadRequestHttpException('type 参数值错误。');
        }
        if ($type == API::OUT_TRADE_NO) {
            return $this->wxService->reverse($sn, $type);
        } else {
            return $this->wxService->reverseByTransactionId($sn);
        }
    }

}
