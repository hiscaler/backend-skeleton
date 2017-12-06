<?php

use yii\db\Migration;

/**
 * 推送位管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m151110_140123_create_label_table extends Migration
{

    public function up()
    {
        $this->createTable('{{%label}}', [
            'id' => $this->primaryKey(),
            'alias' => $this->string(20)->notNull()->unique()->comment('别名'),
            'name' => $this->string(20)->notNull()->comment('推送位名称'),
            'frequency' => $this->integer()->notNull()->defaultValue(0)->comment('权重'),
            'ordering' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%label}}');
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
