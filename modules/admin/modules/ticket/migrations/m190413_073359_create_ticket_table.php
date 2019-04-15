<?php

use yii\db\Migration;

/**
 * 工单管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m190413_073359_create_ticket_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('问题类型'),
            'title' => $this->string(100)->notNull()->comment('标题'),
            'description' => $this->text()->notNull()->comment('问题描述'),
            'confidential_information' => $this->text()->comment('机密信息'),
            'mobile_phone' => $this->string(12)->comment('手机号码'),
            'email' => $this->string(100)->comment('邮箱'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ticket}}');
    }

}
