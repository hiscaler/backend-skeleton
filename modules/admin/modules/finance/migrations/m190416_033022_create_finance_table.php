<?php

use yii\db\Migration;

/**
 * 财务管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m190416_033022_create_finance_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%finance}}', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('类型'),
            'money' => $this->integer()->notNull()->comment('金额'),
            'source' => $this->tinyInteger()->defaultValue(0)->comment('来源'),
            'remittance_slip' => $this->string(100)->comment('汇款凭单'),
            'related_key' => $this->string(20)->comment('关联业务'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('状态'),
            'remark' => $this->text()->comment('备注'),
            'member_id' => $this->integer()->notNull()->comment('会员'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%finance}}');
    }
}
