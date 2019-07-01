<?php

use yii\db\Migration;

/**
 * Handles the creation for table `meta`.
 */
class m160904_123424_create_meta_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%meta}}', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string(60)->notNull()->comment('表名称'),
            'key' => $this->string(60)->notNull()->comment('键名'),
            'label' => $this->string()->notNull()->comment('显示名称'),
            'description' => $this->string()->notNull()->comment('描述'),
            'input_type' => $this->string(16)->notNull()->comment('输入类型'),
            'input_candidate_value' => $this->text()->comment('输入候选值'),
            'return_value_type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('返回值类型'),
            'default_value' => $this->string(16)->comment('默认值'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'deleted_by' => $this->integer()->comment('删除人'),
            'deleted_at' => $this->integer()->comment('删除时间'),
        ]);

        $this->createIndex('table_name', '{{%meta}}', ['table_name']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%meta}}');
    }

}
