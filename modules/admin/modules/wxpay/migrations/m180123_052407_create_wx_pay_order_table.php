<?php

use yii\db\Migration;

/**
 * 微信企业付款订单
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180123_052407_create_wx_pay_order_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%wx_pay_order}}', [
            'id' => $this->primaryKey(),
            'mch_appid' => $this->string(32)->notNull()->comment('商户appid'),
            'mchid' => $this->string(32)->notNull()->comment('商户号'),
            'device_info' => $this->string(32)->comment('设备号'),
            'nonce_str' => $this->string(32)->notNull()->comment('随机字符串'),
            'sign' => $this->string(32)->notNull()->comment('签名'),
            'partner_trade_no' => $this->string(32)->notNull()->comment('商户订单号'),
            'payment_no' => $this->string(32)->comment('微信订单号'),
            'transfer_time' => $this->integer()->comment('转账时间'),
            'payment_time' => $this->integer()->comment('微信支付成功时间'),
            'openid' => $this->string(128)->notNull()->comment('用户标识'),
            'check_name' => $this->string(12)->notNull()->defaultValue('NO_CHECK')->comment('校验用户姓名选项'),
            're_user_name' => $this->string(30)->comment('收款用户姓名'),
            'amount' => $this->integer()->notNull()->comment('金额'),
            'desc' => $this->string()->notNull()->comment('企业付款描述信息'),
            'spbill_create_ip' => $this->string(32)->notNull()->comment('Ip地址'),
            'status' => $this->string(16)->notNull()->comment('转账状态'),
            'reason' => $this->string()->comment('失败原因'),
            'created_at' => $this->integer()->notNull()->comment('提交时间'),
            'created_by' => $this->integer()->notNull()->comment('提交人'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%wx_pay_order}}');
    }
}
