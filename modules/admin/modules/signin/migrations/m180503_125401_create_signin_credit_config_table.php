<?php

use yii\db\Migration;

/**
 * 签到积分设置
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180503_125401_create_signin_credit_config_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%signin_credit_config}}', [
            'id' => $this->primaryKey(),
            'message' => $this->string(60)->notNull()->comment('消息'),
            'credits' => $this->smallInteger()->notNull()->defaultValue(1)->comment('积分'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%signin_credit_config}}');
    }
}
