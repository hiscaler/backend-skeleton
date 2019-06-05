<?php

namespace app\modules\admin\modules\exam\models;

/**
 * This is the model class for table "{{%exam_score_detail}}".
 *
 * @property int $id
 * @property int $score_id 成绩 ID
 * @property int $bank_id 题库
 * @property int $question_id 试题
 * @property string $answer 答案
 * @property int $answer_datetime 答题时间
 * @property int $status 状态
 * @property int $score 该题得分
 */
class ScoreDetail extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exam_score_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['score_id', 'question_id'], 'required'],
            [['score_id', 'bank_id', 'question_id', 'answer_datetime', 'status', 'score'], 'integer'],
            [['answer'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'score_id' => 'Score ID',
            'bank_id' => 'Bank ID',
            'question_id' => 'Question ID',
            'answer' => 'Answer',
            'answer_datetime' => 'Answer Datetime',
            'status' => 'Status',
            'score' => 'Score',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->status = Score::STATUS_PENDING;
                $this->score = 0;
            }

            return true;
        } else {
            return false;
        }
    }

}
