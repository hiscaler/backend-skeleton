<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_score}}`.
 */
class m190605_073430_create_exam_score_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_score}}', [
            'id' => $this->primaryKey(),
            'flag' => $this->string(30)->comment('标志'),
            'begin_datetime' => $this->integer()->notNull()->comment('开始时间'),
            'end_datetime' => $this->integer()->comment('结束时间'),
            'questions_count' => $this->smallInteger()->notNull()->comment('试题数量'),
            'last_answer_datetime' => $this->integer()->comment('最后答题时间'),
            'status' => $this->boolean()->notNull()->defaultValue(0)->comment('状态'),
            'score' => $this->smallInteger()->notNull()->defaultValue(0)->comment('得分'),
            'member_id' => $this->integer()->notNull()->comment('会员'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_score}}');
    }

}
