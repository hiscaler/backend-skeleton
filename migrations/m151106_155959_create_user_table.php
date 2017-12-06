<?php

use yii\db\Migration;

/**
 * 系统用户表
 */
class m151106_155959_create_user_table extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'type' => $this->boolean()->notNull()->defaultValue(0)->comment('用户类型'),
            'username' => $this->string(20)->notNull()->unique()->comment('用户名'),
            'nickname' => $this->string(20)->notNull()->comment('昵称'),
            'avatar' => $this->string(100)->comment('头像'),
            'auth_key' => $this->string(32)->notNull()->comment('认证 key'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'password_reset_token' => $this->string()->unique()->comment('密码重置 token'),
            'email' => $this->string(50)->comment('邮箱'),
            'role' => $this->smallInteger()->notNull()->defaultValue(0)->comment('角色'),
            'credits_count' => $this->integer()->notNull()->defaultValue(0)->comment('积分'),
            'user_group' => $this->string(20)->comment('用户组'),
            'system_group' => $this->string(20)->comment('系统组'),
            'register_ip' => $this->integer()->notNull()->comment('注册 IP'),
            'login_count' => $this->integer()->notNull()->defaultValue(0)->comment('登录次数'),
            'last_login_ip' => $this->integer()->defaultValue(null)->comment('最后登录 IP'),
            'last_login_time' => $this->integer()->defaultValue(null)->comment('最后登录时间'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
