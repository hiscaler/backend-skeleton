<?php

use yii\db\Migration;

/**
 * 微信会员资料
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m171206_153127_create_wechat_member_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%wechat_member}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->comment('会员 id'),
            'subscribe' => $this->boolean()->notNull()->defaultValue(1)->comment('是否关注'),
            'openid' => $this->string(28)->notNull()->unique()->comment('openid'),
            'nickname' => $this->string(50)->notNull()->comment('昵称'),
            'sex' => $this->smallInteger()->notNull()->defaultValue(0)->comment('性别'),
            'country' => $this->string(50)->comment('国家'),
            'province' => $this->string(50)->comment('省份'),
            'city' => $this->string(50)->comment('城市'),
            'language' => $this->string(50)->comment('语言'),
            'headimgurl' => $this->string(200)->comment('头像'),
            'subscribe_time' => $this->integer()->notNull()->comment('头像'),
            'unionid' => $this->string(29)->comment('unionid'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%wechat_member}}');
    }
}
