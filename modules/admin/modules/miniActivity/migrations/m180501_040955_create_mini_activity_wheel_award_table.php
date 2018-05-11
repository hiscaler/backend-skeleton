<?php

use yii\db\Migration;

/**
 * 大转盘奖品设置
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180501_040955_create_mini_activity_wheel_award_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mini_activity_wheel_award}}', [
            'id' => $this->primaryKey(),
            'wheel_id' => $this->integer()->notNull()->comment('转盘 id'),
            'ordering' => $this->smallInteger()->notNull()->defaultValue(1)->comment('排序'),
            'title' => $this->string(60)->notNull()->comment('名称'),
            'description' => $this->text()->comment('描述'),
            'photo' => $this->string(100)->comment('奖品图片'),
            'total_quantity' => $this->integer()->notNull()->defaultValue(0)->comment('总奖品数量'),
            'remaining_quantity' => $this->integer()->notNull()->defaultValue(0)->comment('剩余奖品数量'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mini_activity_wheel_award}}');
    }
}
