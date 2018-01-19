<?php

namespace app\modules\admin\modules\wxpay\models;

use Yii;

/**
 * This is the model class for table "{{%wx_order}}".
 *
 * @property int $id
 * @property string $appid appid
 * @property string $mch_id 商户号
 * @property string $device_info 设备号
 * @property string $nonce_str 随机字符串
 * @property string $sign 签名
 * @property string $sign_type 签名类型
 * @property string $transaction_id 微信订单号
 * @property string $out_trade_no 商户订单号
 * @property string $body 商品描述
 * @property string $detail 商品详情
 * @property string $attach 附加数据
 * @property string $fee_type 标价币种
 * @property int $total_fee 标价金额
 * @property string $spbill_create_ip 终端IP
 * @property int $time_start 交易起始时间
 * @property int $time_expire 交易结束时间
 * @property string $goods_tag 订单优惠标记
 * @property string $trade_type 交易类型
 * @property string $product_id 商品ID
 * @property string $limit_pay 指定支付方式
 * @property string $openid 用户标识
 * @property int $status 状态
 */
class Order extends \yii\db\ActiveRecord
{

    const STATUS_PENDING = 0;
    const STATUS_NOTIFIED = 1;
    const STATUS_REFUND = 2;
    const STATUS_PARTIAL_REFUND = 3;
    const STATUS_CANCEL = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wx_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appid', 'mch_id', 'nonce_str', 'sign', 'out_trade_no', 'total_fee', 'spbill_create_ip', 'time_start', 'openid'], 'required'],
            [['detail'], 'string'],
            [['total_fee', 'time_start', 'time_expire', 'status'], 'integer'],
            [['appid', 'mch_id', 'device_info', 'nonce_str', 'sign', 'sign_type', 'transaction_id', 'out_trade_no', 'goods_tag', 'product_id', 'limit_pay'], 'string', 'max' => 32],
            [['body', 'openid'], 'string', 'max' => 128],
            [['attach'], 'string', 'max' => 127],
            [['fee_type', 'spbill_create_ip', 'trade_type'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'appid' => 'appid',
            'mch_id' => '商户号',
            'device_info' => '设备号',
            'nonce_str' => '随机字符串',
            'sign' => '签名',
            'sign_type' => '签名类型',
            'transaction_id' => '微信订单号',
            'out_trade_no' => '商户订单号',
            'body' => '商品描述',
            'detail' => '商品详情',
            'attach' => '附加数据',
            'fee_type' => '币种',
            'total_fee' => '金额',
            'spbill_create_ip' => '终端IP',
            'time_start' => '交易起始时间',
            'time_expire' => '交易结束时间',
            'goods_tag' => '订单优惠标记',
            'trade_type' => '交易类型',
            'product_id' => '商品ID',
            'limit_pay' => '指定支付方式',
            'openid' => '用户标识',
            'status' => '状态',
        ];
    }

    /**
     * 订单状态选项
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '待通知',
            self::STATUS_NOTIFIED => '已通知',
            self::STATUS_REFUND => '已退款',
            self::STATUS_PARTIAL_REFUND => '部分退款',
            self::STATUS_CANCEL => '取消',
        ];
    }

}
