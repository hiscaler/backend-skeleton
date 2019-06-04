<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_question_bank}}`.
 */
class m190604_063359_create_exam_question_bank_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_question_bank}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(60)->notNull()->comment('题库名称'),
            'description' => $this->string(200)->notNull()->comment('题库说明'),
            'icon' => $this->string(100)->comment('题库图标'),
            'questions_count' => $this->smallInteger()->notNull()->defaultValue(0)->comment('试题数量'),
            'participation_times' => $this->smallInteger()->notNull()->defaultValue(0)->comment('参与次数'),
            'status' => $this->boolean()->notNull()->defaultValue(0)->comment('状态'),
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
        $this->dropTable('{{%exam_question_bank}}');
    }
}
