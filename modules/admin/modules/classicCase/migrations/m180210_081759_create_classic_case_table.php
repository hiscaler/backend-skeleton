<?php

use yii\db\Migration;

/**
 * Handles the creation of table `classic_case`.
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180210_081759_create_classic_case_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%classic_case}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('所属分类'),
            'title' => $this->string(160)->notNull()->comment('标题'),
            'keywords' => $this->string(60)->comment('关键词'),
            'description' => $this->text()->comment('描述'),
            'content' => $this->text()->notNull()->comment('正文'),
            'picture_path' => $this->string(200)->comment('案例图片'),
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
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%classic_case}}');
    }
}
