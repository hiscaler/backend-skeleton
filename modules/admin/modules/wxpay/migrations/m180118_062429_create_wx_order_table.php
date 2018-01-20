<?php

use yii\db\Migration;

/**
 * 微信支付订单管理
 *
 * @link https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180118_062429_create_wx_order_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%wx_order}}', [
            'id' => $this->primaryKey(),
            'appid' => $this->string(32)->notNull()->comment('appid'),
            'mch_id' => $this->string(32)->notNull()->comment('商户号'),
            'device_info' => $this->string(32)->comment('设备号'),
            'nonce_str' => $this->string(32)->notNull()->comment('随机字符串'),
            'sign' => $this->string(32)->notNull()->comment('签名'),
            'sign_type' => $this->string(32)->notNull()->defaultValue('MD5')->comment('签名类型'),
            'transaction_id' => $this->string(32)->comment('微信订单号'),
            'out_trade_no' => $this->string(32)->notNull()->comment('商户订单号'),
            'body' => $this->string(128)->comment('商品描述'),
            'detail' => $this->text()->comment('商品详情'),
            'attach' => $this->string(127)->comment('附加数据'),
            'fee_type' => $this->string(16)->notNull()->defaultValue('CNY')->comment('货币种类'),
            'total_fee' => $this->integer()->notNull()->comment('订单金额'),
            'spbill_create_ip' => $this->string(16)->notNull()->comment('终端IP'),
            'time_start' => $this->integer()->notNull()->comment('交易起始时间'),
            'time_expire' => $this->integer()->comment('交易结束时间'),
            'goods_tag' => $this->string(32)->comment('订单优惠标记'),
            'trade_type' => $this->string(16)->notNull()->defaultValue('JSAPI')->comment('交易类型'),
            'product_id' => $this->string(32)->comment('商品ID'),
            'limit_pay' => $this->string(32)->comment('指定支付方式'),
            'openid' => $this->string(128)->notNull()->comment('用户标识'),
            'status' => $this->smallInteger()->defaultValue(0)->comment('状态'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%wx_order}}');
    }
}
