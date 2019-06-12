<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%set}}`.
 */
class m190611_011528_create_set_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%set}}', [
            'key' => $this->string(100)->notNull()->unique()->comment('键值'),
            'value' => $this->text()->notNull()->comment('值'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%set}}');
    }

}
