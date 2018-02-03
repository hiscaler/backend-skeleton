<?php

use yii\db\Migration;

/**
 * Handles the creation of table `example_demo`.
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180112_083711_create_example_demo_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%example_demo}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%example_demo}}');
    }
}
