<?php

namespace app\modules\admin\modules\wxpay\models;

use app\models\User;

/**
 * This is the model class for table "{{%wx_order_refund}}".
 *
 * @property int $id
 * @property int $order_id 订单 id
 * @property string $appid appid
 * @property string $mch_id 商户号
 * @property string $nonce_str 随机字符串
 * @property string $sign 签名
 * @property string $sign_type 签名类型
 * @property string $transaction_id 微信订单号
 * @property string $out_trade_no 商户订单号
 * @property string $out_refund_no 商户退款单号
 * @property int $total_fee 订单金额
 * @property string $refund_id 微信退款单号
 * @property int $refund_fee 退款金额
 * @property string $refund_fee_type 货币种类
 * @property string $refund_desc 退款原因
 * @property string $refund_account 退款资金来源
 * @property int $created_at 提交时间
 * @property int $created_by 提交人
 */
class OrderRefund extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wx_order_refund}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'appid', 'mch_id', 'nonce_str', 'sign', 'total_fee', 'refund_fee', 'created_at', 'created_by'], 'required'],
            [['order_id', 'total_fee', 'refund_fee', 'created_at', 'created_by'], 'integer'],
            [['appid', 'mch_id', 'nonce_str', 'sign', 'sign_type', 'transaction_id', 'out_trade_no', 'refund_id'], 'string', 'max' => 32],
            [['out_refund_no'], 'string', 'max' => 64],
            [['refund_fee_type'], 'string', 'max' => 8],
            [['refund_desc'], 'string', 'max' => 80],
            [['refund_account'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单 id',
            'appid' => 'appid',
            'mch_id' => '商户号',
            'nonce_str' => '随机字符串',
            'sign' => '签名',
            'sign_type' => '签名类型',
            'transaction_id' => '微信订单号',
            'out_trade_no' => '商户订单号',
            'out_refund_no' => '商户退款单号',
            'total_fee' => '订单金额',
            'refund_id' => '微信退款单号',
            'refund_fee' => '退款金额',
            'refund_fee_type' => '货币种类',
            'refund_desc' => '退款原因',
            'refund_account' => '退款资金来源',
            'created_at' => '提交时间',
            'created_by' => '提交人',
            'creater.nickname' => '提交人',
        ];
    }

    /**
     * 退款人
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreater()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

}
