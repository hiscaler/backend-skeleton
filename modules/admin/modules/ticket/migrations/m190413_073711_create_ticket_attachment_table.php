<?php

use yii\db\Migration;

/**
 * 工单附件
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m190413_073711_create_ticket_attachment_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket_attachment}}', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull()->comment('所属工单'),
            'path' => $this->string(100)->notNull()->comment('附件地址'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ticket_attachment}}');
    }
}
