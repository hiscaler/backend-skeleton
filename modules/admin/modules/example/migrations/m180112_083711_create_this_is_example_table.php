<?php

use yii\db\Migration;

/**
 * Handles the creation of table `this_is_example`.
 */
class m180112_083711_create_this_is_example_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%this_is_example}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%this_is_example}}');
    }
}
