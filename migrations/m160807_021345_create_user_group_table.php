<?php

use yii\db\Migration;

/**
 * 用户分组
 */
class m160807_021345_create_user_group_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user_group}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull()->comment('分组类型'),
            'alias' => $this->string(20)->notNull()->unique()->comment('别名'),
            'name' => $this->string(30)->notNull()->comment('组头衔'),
            'icon_path' => $this->string(100)->comment('组图标'),
            'min_credits' => $this->integer()->notNull()->defaultValue(0)->comment('最小积分'),
            'max_credits' => $this->integer()->notNull()->defaultValue(0)->comment('最大积分'),
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
        $this->dropTable('{{%user_group}}');
    }

}
