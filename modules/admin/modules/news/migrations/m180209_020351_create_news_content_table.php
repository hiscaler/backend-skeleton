<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news_content`.
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180209_020351_create_news_content_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%news_content}}', [
            'news_id' => $this->integer()->notNull()->unique()->comment('资讯 id'),
            'content' => $this->text()->notNull()->comment('正文'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%news_content}}');
    }
}
