<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member_login_log}}`.
 */
class m200225_143529_create_member_login_log_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%member_login_log}}', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull()->comment('会员'),
            'ip' => $this->string(39)->notNull()->comment('登录 IP'),
            'login_at' => $this->integer()->notNull()->comment('登录时间'),
            'client_information' => $this->string()->notNull()->comment('客户端信息'),
        ]);
        $this->createIndex('member_id_login_at', '{{%member_login_log}}', ['member_id', 'login_at']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%member_login_log}}');
    }

}
