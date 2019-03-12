<?php

use yii\db\Migration;

/**
 * 通知
 * Handles the creation of table `notice`.
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m190312_073517_create_notice_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notice}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('所属分类'),
            'title' => $this->string(160)->notNull()->comment('标题'),
            'description' => $this->text()->comment('描述'),
            'content' => $this->text()->notNull()->comment('正文'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'clicks_count' => $this->integer()->notNull()->defaultValue(0)->comment('点击次数'),
            'published_at' => $this->integer()->notNull()->comment('发布时间'),
            'ordering' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
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
        $this->dropTable('{{%notice}}');
    }
}
