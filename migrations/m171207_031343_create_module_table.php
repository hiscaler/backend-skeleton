<?php

use yii\db\Migration;

/**
 * 模块管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m171207_031343_create_module_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%module}}', [
            'id' => $this->primaryKey(),
            'alias' => $this->string(20)->notNull()->unique()->comment('别名'),
            'name' => $this->string(30)->notNull()->comment('模块名称'),
            'author' => $this->string(20)->notNull()->comment('作者'),
            'version' => $this->string(10)->notNull()->comment('版本'),
            'icon' => $this->string(100)->comment('图标'),
            'url' => $this->string(100)->comment('URL'),
            'description' => $this->string()->comment('描述'),
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
        $this->dropTable('{{%module}}');
    }
}
