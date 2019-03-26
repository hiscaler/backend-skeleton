<?php

namespace app\modules\admin\modules\wechat\models;

/**
 * This is the model class for table "{{%wechat_pay_order}}".
 *
 * @property int $id
 * @property string $mch_appid 商户appid
 * @property string $mchid 商户号
 * @property string $device_info 设备号
 * @property string $nonce_str 随机字符串
 * @property string $sign 签名
 * @property string $partner_trade_no 商户订单号
 * @property string $payment_no 微信订单号
 * @property int $transfer_time 转账时间
 * @property int $payment_time 微信支付成功时间
 * @property string $openid 用户标识
 * @property string $check_name 校验用户姓名选项
 * @property string $re_user_name 收款用户姓名
 * @property int $amount 金额
 * @property string $desc 企业付款描述信息
 * @property string $spbill_create_ip Ip地址
 * @property string $status 转账状态
 * @property string $reason 失败原因
 * @property int $created_at 提交时间
 * @property int $created_by 提交人
 */
class PayOrder extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_pay_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mch_appid', 'mchid', 'nonce_str', 'sign', 'partner_trade_no', 'openid', 'amount', 'desc', 'spbill_create_ip', 'status', 'created_at', 'created_by'], 'required'],
            [['transfer_time', 'payment_time', 'amount', 'created_at', 'created_by'], 'integer'],
            [['mch_appid', 'mchid', 'device_info', 'nonce_str', 'sign', 'partner_trade_no', 'payment_no', 'spbill_create_ip'], 'string', 'max' => 32],
            [['openid'], 'string', 'max' => 128],
            [['check_name'], 'string', 'max' => 12],
            [['re_user_name'], 'string', 'max' => 30],
            [['desc', 'reason'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mch_appid' => '商户appid',
            'mchid' => '商户号',
            'device_info' => '设备号',
            'nonce_str' => '随机字符串',
            'sign' => '签名',
            'partner_trade_no' => '商户订单号',
            'payment_no' => '微信订单号',
            'transfer_time' => '转账时间',
            'payment_time' => '微信支付成功时间',
            'openid' => '用户标识',
            'check_name' => '校验用户姓名选项',
            're_user_name' => '收款用户姓名',
            'amount' => '金额',
            'desc' => '企业付款描述信息',
            'spbill_create_ip' => 'Ip地址',
            'status' => '转账状态',
            'reason' => '失败原因',
            'created_at' => '提交时间',
            'created_by' => '提交人',
        ];
    }
}
