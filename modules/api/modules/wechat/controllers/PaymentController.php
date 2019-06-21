<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\modules\wechat\models\Order;
use app\modules\api\modules\wechat\models\PrepareOrder;
use EasyWeChat\Payment\API;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * 支付
 * Class PaymentController
 *
 * @property \EasyWeChat\Payment\Payment $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PaymentController extends Controller
{

    private $_notify_url;

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->payment;
        $notifyUrl = isset($this->wxConfig['payment']['notify_url']) ? $this->wxConfig['payment']['notify_url'] : null;
        if (empty($notifyUrl)) {
            $this->_notify_url = Url::toRoute(['/api/wechat/payment/notify'], true);
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
     * @throws ServerErrorHttpException
     * @throws \yii\db\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionOrder()
    {
        $model = new PrepareOrder();
        $model->notify_url = $this->_notify_url;
        $model->appid = $this->wxConfig['app_id'];
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->out_trade_no || $model->generateOutTradeNo();
        if ($model->validate()) {
            $allowedAttributes = [
                'body',
                'detail',
                'attach',
                'out_trade_no',
                'fee_type',
                'total_fee',
                'spbill_create_ip',
                'time_start',
                'time_expire',
                'goods_tag',
                'notify_url',
                'trade_type',
                'product_id',
                'limit_pay',
                'openid',
                'sub_openid',
                'auth_code',
            ];
            $attributes = [
                'notify_url' => $model->notify_url,
            ];
            foreach ($model->toArray() as $key => $value) {
                if (in_array($key, $allowedAttributes)) {
                    $attributes[$key] = $value;
                }
            }

            $response = $this->wxService->prepare(new \EasyWeChat\Payment\Order($attributes));
            if ($response->return_code == 'SUCCESS' && $response->result_code == 'SUCCESS') {
                $prepayId = $response->prepay_id;
                switch ($model->trade_type) {
                    case \EasyWeChat\Payment\Order::NATIVE:
                        $config = $this->wxService->configForJSSDKPayment($prepayId);
                        $config['code_url'] = $response->get('code_url');
                        $config['out_trade_no'] = $model->out_trade_no;
                        break;

                    case \EasyWeChat\Payment\Order::APP:
                        $config = $this->wxService->configForAppPayment($prepayId);
                        break;

                    default:
                        $config = $this->wxService->configForPayment($prepayId, false);
                        break;
                }

                $orderId = \Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%wechat_order}} WHERE [[out_trade_no]] = :outTradeNo', [
                    ':outTradeNo' => $model->out_trade_no,
                ])->queryScalar();
                if (!$orderId) {
                    // 创建商户订单
                    $model->nonce_str = $config['nonceStr'];
                    $model->sign = $config['paySign'];
                    $model->sign_type = $config['signType'];
                    if ($model->save()) {
                        $config['id'] = $model->id;

                        return $config;
                    } elseif (!$model->hasErrors()) {
                        throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
                    }

                    return $model;
                } else {
                    $config['id'] = $orderId;

                    return $config;
                }
            } else {
                throw new BadRequestHttpException('支付失败（' . ($response->err_code_des ?: $response->return_msg) . '）。');
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
            $order = Order::findOne(['appid' => $notify['appid'], 'out_trade_no' => $notify['out_trade_no']]);
            if ($order === null) {
                throw new NotFoundHttpException('ORDER NOT FOUND');
            }
            if ($successful) {
                if ($order->status == Order::STATUS_PENDING) {
                    !$order->openid && $order->openid = $notify['openid'];
                    $order->transaction_id = $notify['transaction_id'];
                    $timeEnd = $notify['time_end'];
                    $year = substr($timeEnd, 0, 4);
                    list($month, $day, $hour, $minute, $second) = str_split(substr($timeEnd, 4), 2);
                    $order->time_end = (new \DateTime())->setDate($year, $month, $day)->setTime($hour, $minute, $second)->getTimestamp();

                    if (isset($notify['trade_state'])) {
                        $order->trade_state = $notify['trade_state'];
                        $order->trade_state_desc = $notify['trade_state_desc'];
                    }

                    if (isset($this->wxConfig['business']['class']) && class_exists($this->wxConfig['business']['class'])) {
                        try {
                            $class = $this->wxConfig['business']['class'];
                            $success = call_user_func([new $class(), 'process'], $order);
                            if ($success) {
                                $order->status = Order::STATUS_SUCCESS;

                                return $order->save();
                            } else {
                                throw new BadRequestHttpException("订单处理失败。");
                            }
                        } catch (\Exception $e) {
                            Yii::error($e->getMessage(), 'api');

                            return false;
                        }
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            } else {
                if (isset($notify['trade_state'])) {
                    $order->trade_state = $notify['trade_state'];
                    $order->trade_state_desc = $notify['trade_state_desc'];
                }
                $order->status = Order::STATUS_FAIL;

                return $order->save();
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
