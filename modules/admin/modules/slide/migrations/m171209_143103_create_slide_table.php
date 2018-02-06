<?php

use yii\db\Migration;

/**
 * Handles the creation of table `slide`.
 */
class m171209_143103_create_slide_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%slide}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('所属分类'),
            'title' => $this->string(60)->notNull()->comment('标题'),
            'url' => $this->string(100)->comment('链接地址'),
            'url_open_target' => $this->string(6)->notNull()->defaultValue('_blank')->comment('链接打开方式'),
            'picture_path' => $this->string(100)->notNull()->comment('图片'),
            'ordering' => $this->smallInteger()->notNull()->defaultValue(0)->comment('排序'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
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
        $this->dropTable('{{%slide}}');
    }
}
