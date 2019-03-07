<?php

namespace app\modules\api\modules\wechat\models;

use yii\base\Model;

/**
 * 订单
 * Class Order
 *
 * @package app\modules\api\modules\wechat\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class Order extends Model
{

    public $openid;
    public $trade_type;
    public $body;
    public $detail;
    public $out_trade_no;
    public $total_fee;
    public $notify_url;

    public function rules()
    {
        return [
            [['trade_type', 'body', 'out_trade_no', 'total_fee'], 'required'],
            ['openid', 'string', 'max' => 128],
            ['openid', 'required', 'when' => function ($model) {
                return $model->trade_type == \EasyWeChat\Payment\Order::JSAPI;
            }],
            [['trade_type', 'body', 'detail', 'out_trade_no'], 'trim'],
            ['body', 'string', 'max' => 128],
            ['detail', 'string', 'max' => 6000],
            ['out_trade_no', 'string', 'max' => 32],
            [['trade_type'], 'strtolower'],
            [['trade_type'], 'default', 'value' => self::TRADE_TYPE_JSAPI],
            ['total_fee', 'integer', 'min' => 1],
            ['notify_url', 'string', 'max' => 256],
            ['openid', function ($attribute, $params) {
                if ($this->trade_type == \EasyWeChat\Payment\Order::JSAPI) {
                    $exist = \Yii::$app->getDb()->createCommand('SELECT COUNT(*) FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $this->openid])->queryScalar();
                    if (!$exist) {
                        $this->addError($attribute, 'openid 不存在。');
                    }
                }
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'trade_type' => '交易类型',
            'body' => '商品描述',
            'detail' => '商品详情',
            'out_trade_no' => '商户订单号',
            'total_fee' => '标价金额',
            'notify_url' => '通知地址',
        ];
    }

    /**
     * 支付方式
     *
     * @return array
     */
    public static function tradeTypeOptions()
    {
        return [
            \EasyWeChat\Payment\Order::JSAPI => 'JSAPI 支付',
            \EasyWeChat\Payment\Order::NATIVE => '扫一扫支付',
            \EasyWeChat\Payment\Order::APP => 'APP 支付',
            \EasyWeChat\Payment\Order::MICROPAY => '小程序支付',
        ];
    }

}