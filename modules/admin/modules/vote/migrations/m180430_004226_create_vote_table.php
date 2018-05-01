<?php

use yii\db\Migration;

/**
 * Handles the creation of table `votes`.
 */
class m180430_004226_create_vote_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vote}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('分类'),
            'title' => $this->string(100)->notNull()->comment('名称'),
            'description' => $this->text()->comment('描述'),
            'begin_datetime' => $this->integer()->notNull()->comment('开始时间'),
            'end_datetime' => $this->integer()->notNull()->comment('结束时间'),
            'total_votes_count' => $this->integer()->notNull()->defaultValue(0)->comment('总票数'),
            'allow_anonymous' => $this->boolean()->notNull()->defaultValue(0)->comment('允许匿名投票'),
            'allow_view_results' => $this->boolean()->notNull()->defaultValue(0)->comment('允许查看结果'),
            'allow_multiple_choice' => $this->boolean()->notNull()->defaultValue(0)->comment('允许多选'),
            'interval_seconds' => $this->integer()->notNull()->defaultValue(0)->comment('间隔时间'),
            'ordering' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
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
        $this->dropTable('{{%vote}}');
    }
}
