<?php

use yii\db\Migration;

/**
 * 通知查看权限
 * Handles the creation of table `{{%notice_permission}}`.
 */
class m190321_034101_create_notice_permission_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notice_permission}}', [
            'id' => $this->primaryKey(),
            'notice_id' => $this->integer()->notNull()->comment('通知'),
            'member_id' => $this->integer()->notNull()->comment('会员'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%notice_permission}}');
    }

}
