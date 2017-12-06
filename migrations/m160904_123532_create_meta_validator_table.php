<?php

use yii\db\Migration;

/**
 * Handles the creation for table `meta_validator`.
 */
class m160904_123532_create_meta_validator_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%meta_validator}}', [
            'id' => $this->primaryKey(),
            'meta_id' => $this->integer()->notNull()->comment('Meta id'),
            'name' => $this->string(30)->notNull()->comment('验证器名称'),
            'options' => $this->text()->comment('验证器配置属性'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%meta_validator}}');
    }

}
