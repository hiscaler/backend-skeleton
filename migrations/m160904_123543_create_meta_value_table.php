<?php

use yii\db\Migration;

/**
 * Handles the creation for table `meta_value`.
 */
class m160904_123543_create_meta_value_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%meta_value}}', [
            'meta_id' => $this->integer()->notNull()->comment('Meta id'),
            'object_id' => $this->integer()->notNull()->comment('数据 id'),
            'value' => $this->text()->notNull()->comment('值'),
        ]);

        // Create index
        $this->createIndex('meta_id_object_id', '{{%meta_value}}', ['meta_id', 'object_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%meta_value}}');
    }

}
