<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mini_activity_wheel_log`.
 */
class m180501_060118_create_mini_activity_wheel_log_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mini_activity_wheel_log}}', [
            'id' => $this->primaryKey(),
            'wheel_id' => $this->integer()->notNull()->comment('大转盘 id'),
            'is_win' => $this->boolean()->notNull()->defaultValue(0)->comment('是否获奖'),
            'award_id' => $this->integer()->notNull()->defaultValue(0)->comment('奖品选项'),
            'ip_address' => $this->string(15)->notNull()->comment('IP 地址'),
            'post_datetime' => $this->integer()->notNull()->comment('提交时间'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('会员'),
            'is_get' => $this->boolean(16)->defaultValue(0)->comment('是否兑奖'),
            'get_password' => $this->string(16)->comment('兑奖密码'),
            'get_datetime' => $this->integer()->comment('兑奖时间'),
            'remark' => $this->text()->comment('备注'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mini_activity_wheel_log}}');
    }
}
