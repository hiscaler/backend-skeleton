<?php

use yii\db\Migration;

/**
 * 友情链接
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180129_142148_create_link_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%link}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('分类'),
            'type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('类型'),
            'title' => $this->string(60)->notNull()->comment('标题'),
            'description' => $this->string(100)->notNull()->comment('描叙'),
            'url' => $this->string(100)->notNull()->comment('URL'),
            'url_open_target' => $this->string(6)->notNull()->defaultValue('_blank')->comment('链接打开方式'),
            'logo' => $this->string(100)->comment('Logo'),
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
        $this->dropTable('{{%link}}');
    }
}
