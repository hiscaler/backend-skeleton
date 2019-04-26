<?php

use yii\db\Migration;

/**
 * 工单消息
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m190425_060653_create_ticket_message_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket_message}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull()->comment('工单'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类型'),
            'content' => $this->text()->notNull()->comment('消息'),
            'parent_id' => $this->integer()->notNull()->defaultValue(0)->comment('引用消息'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('会员'),
            'reply_user_id' => $this->integer()->notNull()->defaultValue(0)->comment('回复人'),
            'reply_username' => $this->string(10)->comment('回复人'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ticket_message}}');
    }

}