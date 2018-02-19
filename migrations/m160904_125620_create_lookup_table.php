<?php

use yii\db\Migration;

/**
 * Handles the creation for table `lookup`.
 */
class m160904_125620_create_lookup_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%lookup}}', [
            'id' => $this->primaryKey(),
            'type' => $this->boolean()->notNull()->defaultValue(1)->comment('类型（0:私有 1: 公有）'),
            'group' => $this->smallInteger()->notNull()->defaultValue(0)->comment('分组（0: 自定义　1: 系统 2: SEO）'),
            'key' => $this->string(60)->notNull()->comment('键名'),
            'label' => $this->string(60)->notNull()->comment('标签'),
            'description' => $this->text()->comment('描述'),
            'value' => $this->text()->notNull()->comment('值'),
            'return_type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('返回值类型'),
            'input_method' => $this->string(12)->notNull()->defaultValue('string')->comment('输入方式'),
            'input_value' => $this->text()->comment('输入值'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('添加人'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('更新人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
        ]);

        $this->createIndex('key', '{{%lookup}}', ['key']);
        $this->createIndex('updated_at', '{{%lookup}}', ['updated_at']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%lookup}}');
    }

}
