<?php

use yii\db\Migration;

/**
 * 分类管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m151114_032819_create_category_table extends Migration
{

    public function up()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'sign' => $this->string(40)->unique()->comment('标记'),
            'alias' => $this->string(120)->notNull()->comment('分类别名'),
            'name' => $this->string(30)->notNull()->comment('分类名称'),
            'short_name' => $this->string(30)->notNull()->comment('简称'),
            'parent_id' => $this->integer()->notNull()->defaultValue(0)->comment('父级'),
            'level' => $this->smallInteger()->notNull()->defaultValue(0)->comment('层级'),
            'parent_ids' => $this->string(100)->comment('父级 id 集合'),
            'parent_names' => $this->string(255)->comment('父级名称集合'),
            'icon' => $this->string(100)->comment('分类图标'),
            'description' => $this->text()->comment('描述'),
            'ordering' => $this->smallInteger()->notNull()->defaultValue(0)->comment('排序'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);

        $this->createIndex('parent_id', '{{%category}}', ['parent_id']);
    }

    public function down()
    {
        $this->dropTable('{{%category}}');
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
