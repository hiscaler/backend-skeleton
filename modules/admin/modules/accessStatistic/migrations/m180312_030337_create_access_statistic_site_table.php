<?php

use yii\db\Migration;

/**
 * 访问统计站点表
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180312_030337_create_access_statistic_site_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%access_statistic_site}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(30)->notNull()->comment('站点名称'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_statistic_site}}');
    }
}
