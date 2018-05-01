<?php

use yii\db\Migration;

/**
 * 投票选项
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180501_022833_create_vote_option_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vote_option}}', [
            'id' => $this->primaryKey(),
            'vote_id' => $this->integer()->notNull()->comment('投票 id'),
            'ordering' => $this->smallInteger()->notNull()->defaultValue(1)->comment('排序'),
            'title' => $this->string(60)->notNull()->comment('名称'),
            'description' => $this->text()->comment('描述'),
            'photo' => $this->string(100)->comment('图片'),
            'votes_count' => $this->integer()->notNull()->defaultValue(0)->comment('票数'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vote_option}}');
    }
}
