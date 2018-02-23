<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news`.
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180208_150244_create_news_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('所属分类'),
            'title' => $this->string(160)->notNull()->comment('标题'),
            'short_title' => $this->string(160)->notNull()->comment('副标题'),
            'keywords' => $this->string(60)->comment('关键词'),
            'description' => $this->text()->comment('描述'),
            'author' => $this->string(20)->notNull()->comment('作者'),
            'source' => $this->string(30)->notNull()->comment('来源'),
            'source_url' => $this->string(200)->comment('来源 URL'),
            'is_picture_news' => $this->boolean()->notNull()->defaultValue(0)->comment('图片资讯'),
            'picture_path' => $this->string(200)->comment('图片地址'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'enabled_comment' => $this->boolean()->notNull()->defaultValue(0)->comment('激活评论'),
            'comments_count' => $this->integer()->notNull()->defaultValue(0)->comment('评论次数'),
            'clicks_count' => $this->integer()->notNull()->defaultValue(0)->comment('点击次数'),
            'published_at' => $this->integer()->notNull()->comment('发布时间'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);

        $this->createIndex('category_id', '{{%news}}', ['category_id']);
        $this->createIndex('is_picture_news', '{{%news}}', ['is_picture_news']);
        $this->createIndex('enabled', '{{%news}}', ['enabled']);
        $this->createIndex('category_id_enabled', '{{%news}}', ['category_id', 'enabled']);
        $this->createIndex('category_id_is_picture_news', '{{%news}}', ['category_id', 'is_picture_news']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%news}}');
    }
}
