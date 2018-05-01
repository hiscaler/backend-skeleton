<?php

use yii\db\Migration;

/**
 * Handles the creation of table `vote_log`.
 */
class m180430_004234_create_vote_log_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vote_log}}', [
            'id' => $this->primaryKey(),
            'vote_id' => $this->integer()->notNull()->comment('投票 id'),
            'option_id' => $this->integer()->notNull()->comment('投票选项'),
            'ip_address' => $this->string(15)->notNull()->comment('IP 地址'),
            'post_datetime' => $this->integer()->notNull()->comment('投票时间'),
            'member_id' => $this->integer()->notNull()->defaultValue(0)->comment('会员'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vote_log}}');
    }
}
