<?php

use yii\db\Migration;

/**
 * 留言反馈表
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180206_064203_create_feedback_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%feedback}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->defaultValue(0)->comment('分类'),
            'title' => $this->string(100)->comment('标题'),
            'username' => $this->string(20)->comment('姓名'),
            'tel' => $this->string(20)->comment('电话号码'),
            'mobile_phone' => $this->string(11)->comment('手机号码'),
            'email' => $this->string(60)->comment('邮箱'),
            'ip' => $this->string(39)->notNull()->comment('IP 地址'),
            'picture' => $this->string(100)->comment('图片'),
            'message' => $this->text()->notNull()->comment('内容'),
            'response_message' => $this->text()->comment('回复内容'),
            'response_datetime' => $this->integer()->comment('回复时间'),
            'enabled' => $this->boolean()->notNull()->defaultValue(0)->comment('激活'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);

        $this->createIndex('category_id', '{{%feedback}}', ['category_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%feedback}}');
    }

}
