<?php

use yii\db\Migration;

/**
 * 会员
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m171206_153109_create_member_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%member}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull()->defaultValue(0)->comment('分类'),
            'type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('会员类型'),
            'role' => $this->string(64)->comment('角色'),
            'group' => $this->string(20)->comment('分组'),
            'unique_key' => $this->string(32)->notNull()->unique()->comment('唯一码'),
            'parent_id' => $this->integer()->notNull()->defaultValue(0)->comment('上级'),
            'username' => $this->string(20)->notNull()->unique()->comment('帐号'),
            'nickname' => $this->string(60)->notNull()->comment('昵称'),
            'real_name' => $this->string(20)->comment('姓名'),
            'avatar' => $this->string(200)->comment('头像'),
            'auth_key' => $this->string(32)->notNull()->comment('认证 key'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'password_reset_token' => $this->string()->unique()->comment('密码重置 token'),
            'access_token' => $this->string()->unique()->comment('访问 Token'),
            'email' => $this->string(50)->comment('邮箱'),
            'mobile_phone' => $this->string(35)->comment('手机号码'),
            'register_ip' => $this->string(39)->notNull()->comment('注册 IP'),
            'login_count' => $this->integer()->notNull()->defaultValue(0)->comment('登录次数'),
            'total_money' => $this->integer()->notNull()->defaultValue(0)->comment('总金额'),
            'available_money' => $this->integer()->notNull()->defaultValue(0)->comment('可用金额'),
            'total_credits' => $this->integer()->notNull()->defaultValue(0)->comment('总积分'),
            'available_credits' => $this->integer()->notNull()->defaultValue(0)->comment('可用积分'),
            'alarm_credits' => $this->integer()->notNull()->defaultValue(0)->comment('积分警戒值'),
            'last_login_ip' => $this->string(39)->defaultValue(null)->comment('最后登录 IP'),
            'last_login_time' => $this->integer()->defaultValue(null)->comment('最后登录时间'),
            'last_login_session' => $this->string(128)->comment('最后登录 session 值'),
            'expired_datetime' => $this->integer()->defaultValue(null)->comment('有效期'),
            'usable_scope' => $this->smallInteger()->notNull()->defaultValue(0)->comment('使用范围'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
            'remark' => $this->text()->comment('备注'),
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
        $this->dropTable('{{%member}}');
    }

}
