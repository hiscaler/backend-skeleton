<?php

use yii\db\Migration;

/**
 * 会员相关属性
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m190105_013323_create_member_profile_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%member_profile}}', [
            'member_id' => $this->integer()->notNull()->unique()->comment('会员'),
            'tel' => $this->string(30)->comment('电话号码'),
            'address' => $this->string(100)->comment('地址'),
            'zip_code' => $this->string(6)->comment('邮编'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('状态'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%member_profile}}');
    }
}
