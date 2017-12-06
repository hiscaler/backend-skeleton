<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_credit_log`.
 */
class m160824_141157_create_user_credit_log_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user_credit_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('用户 id'),
            'operation' => $this->string(20)->notNull()->comment('积分类型'),
            'related_key' => $this->string(60)->comment('外部关联数据'),
            'credits' => $this->smallInteger()->notNull()->comment('积分'),
            'remark' => $this->text()->comment('备注'),
            'created_at' => $this->integer()->notNull()->comment('操作时间'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('操作人'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%user_credit_log}}');
    }

}
