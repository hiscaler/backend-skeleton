<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_score_detail}}`.
 */
class m190605_074724_create_exam_score_detail_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_score_detail}}', [
            'id' => $this->primaryKey(),
            'score_id' => $this->integer()->notNull()->comment('成绩 ID'),
            'bank_id' => $this->integer()->notNull()->defaultValue(0)->comment('题库'),
            'question_id' => $this->integer()->notNull()->comment('试题'),
            'answer' => $this->string(10)->comment('答案'),
            'answer_datetime' => $this->integer()->comment('答题时间'),
            'status' => $this->boolean()->notNull()->defaultValue(0)->comment('状态'),
            'score' => $this->smallInteger()->notNull()->defaultValue(0)->comment('该题得分'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_score_detail}}');
    }

}
