<?php

use yii\db\Migration;

/**
 * 实体数据标签
 */
class m151110_141038_create_entity_label_table extends Migration
{

    public function up()
    {
        $this->createTable('{{%entity_label}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer()->notNull()->comment('数据 id'),
            'model_name' => $this->string(60)->notNull()->comment('模型名称'),
            'label_id' => $this->integer()->notNull()->comment('推送位 id'),
            'ordering' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);

        $this->createIndex('entity_id_model_name', '{{%entity_label}}', ['entity_id', 'model_name']);
    }

    public function down()
    {
        $this->dropTable('{{%entity_label}}');
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
