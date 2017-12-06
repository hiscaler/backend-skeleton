<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_auth_category`.
 */
class m161113_030737_create_user_auth_category_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%user_auth_category}}', [
            'user_id' => $this->integer()->notNull()->comment('用户 id'),
            'category_id' => $this->integer()->notNull()->comment('分类 id'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%user_auth_category}}');
    }

}
