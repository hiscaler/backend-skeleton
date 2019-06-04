<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_question}}`.
 */
class m190604_063437_create_exam_question_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_question}}', [
            'id' => $this->primaryKey(),
            'question_bank_id' => $this->integer()->notNull()->comment('所属题库'),
            'type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('试题类型'),
            'status' => $this->boolean()->notNull()->defaultValue(1)->comment('试题状态'),
            'content' => $this->text()->notNull()->comment('试题题干'),
            'options' => $this->text()->comment('选项'),
            'answer' => $this->text()->notNull()->comment('答案设置'),
            'resolve' => $this->text()->comment('试题解析'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_question}}');
    }
}
