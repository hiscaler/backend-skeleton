<?php

use yii\db\Migration;

/**
 * 通知查看记录
 * Handles the creation of table `{{%notice_view}}`.
 */
class m190321_033845_create_notice_view_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notice_view}}', [
            'id' => $this->primaryKey(),
            'notice_id' => $this->integer()->notNull()->comment('通知'),
            'member_id' => $this->integer()->notNull()->comment('会员'),
            'view_datetime' => $this->integer()->notNull()->comment('查看时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notice_view}}');
    }

}