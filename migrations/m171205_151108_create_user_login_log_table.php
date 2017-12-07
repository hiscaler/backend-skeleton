<?php

use yii\db\Migration;

/**
 * 用户登录日志
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m171205_151108_create_user_login_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user_login_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('用户 id'),
            'login_ip' => $this->string(15)->notNull()->comment('登录 IP'),
            'client_information' => $this->string()->notNull()->comment('客户端信息'),
            'login_at' => $this->integer()->notNull()->comment('登录时间')

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%user_login_log}}');
    }
}
