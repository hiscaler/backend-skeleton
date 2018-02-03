<?php

use yii\db\Migration;

/**
 * 文章管理表
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180112_025128_create_article_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'alias' => $this->string(60)->notNull(),
            'title' => $this->string(60)->notNull()->comment('标题'),
            'keyword' => $this->string(60)->comment('关键词'),
            'description' => $this->text()->comment('描述'),
            'content' => $this->text()->notNull()->comment('正文'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);

        $this->createIndex('alias', '{{%article}}', ['alias']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%article}}');
    }
}
