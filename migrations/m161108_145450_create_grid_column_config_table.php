<?php

use yii\db\Migration;

/**
 * Handles the creation for table `grid_column_config`.
 */
class m161108_145450_create_grid_column_config_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%grid_column_config}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->notNull()->comment('表格名称'),
            'attribute' => $this->string(30)->notNull()->comment('表格属性'),
            'css_class' => $this->string(120)->comment('CSS 样式'),
            'visible' => $this->boolean()->notNull()->comment('是否可见'),
            'user_id' => $this->integer()->notNull()->comment('用户 id'),
            'tenant_id' => $this->integer()->notNull()->comment('站点 id'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%grid_column_config}}');
    }

}
