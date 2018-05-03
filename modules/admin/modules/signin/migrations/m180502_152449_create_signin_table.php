<?php

use yii\db\Migration;

/**
 * Handles the creation of table `signin`.
 */
class m180502_152449_create_signin_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%signin}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull()->comment('会员 id'),
            'ymd' => $this->integer()->notNull()->comment('签到年月日'),
            'signin_datetime' => $this->integer()->notNull()->comment('签到时间'),
            'credits' => $this->smallInteger()->notNull()->defaultValue(0)->comment('积分'),
            'ip_address' => $this->string(15)->notNull()->comment('IP'),
        ]);
        $this->createIndex('ymd', '{{%signin}}', ['ymd']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%signin}}');
    }
}
