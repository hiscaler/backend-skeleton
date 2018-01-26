<?php

use yii\db\Migration;

/**
 * 订单退款
 *
 * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_4
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180119_085844_create_wechat_refund_order_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%wechat_refund_order}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull()->comment('订单 id'),
            'appid' => $this->string(32)->notNull()->comment('appid'),
            'mch_id' => $this->string(32)->notNull()->comment('商户号'),
            'nonce_str' => $this->string(32)->notNull()->comment('随机字符串'),
            'sign' => $this->string(32)->notNull()->comment('签名'),
            'sign_type' => $this->string(32)->notNull()->defaultValue('MD5')->comment('签名类型'),
            'transaction_id' => $this->string(32)->comment('微信订单号'),
            'out_trade_no' => $this->string(32)->comment('商户订单号'),
            'out_refund_no' => $this->string(64)->comment('商户退款单号'),
            'total_fee' => $this->integer()->notNull()->comment('订单金额'),
            'refund_id' => $this->string(32)->comment('微信退款单号'),
            'refund_fee' => $this->integer()->notNull()->comment('退款金额'),
            'refund_fee_type' => $this->string(8)->notNull()->defaultValue('CNY')->comment('货币种类'),
            'refund_desc' => $this->string(80)->comment('退款原因'),
            'refund_account' => $this->string(30)->comment('退款资金来源'),
            'created_at' => $this->integer()->notNull()->comment('提交时间'),
            'created_by' => $this->integer()->notNull()->comment('提交人'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%wechat_refund_order}}');
    }
}
