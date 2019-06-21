<?php

namespace app\modules\admin\modules\exam\models;

use Yii;

/**
 * This is the model class for table "{{%exam_score}}".
 *
 * @property int $id
 * @property string $flag 标志
 * @property int $begin_datetime 开始时间
 * @property int $end_datetime 结束时间
 * @property int $questions_count 试题数量
 * @property int $last_answer_datetime 最后答题时间
 * @property int $status 状态
 * @property int $score 得分
 * @property int $member_id 会员
 */
class Score extends \yii\db\ActiveRecord
{

    /**
     * 待完成
     */
    const STATUS_PENDING = 0;

    /**
     * 已完成
     */
    const STATUS_FINISHED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exam_score}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin_datetime'], 'required'],
            [['end_datetime', 'questions_count', 'last_answer_datetime', 'status', 'score', 'member_id'], 'integer'],
            ['begin_datetime', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'begin_datetime'],
            [['questions_count', 'score'], 'default', 'value' => 0],
            [['flag'], 'trim'],
            [['flag'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'flag' => 'Flag',
            'begin_datetime' => 'Begin Datetime',
            'end_datetime' => 'End Datetime',
            'questions_count' => 'Questions Count',
            'last_answer_datetime' => 'Last Answer Datetime',
            'status' => 'Status',
            'score' => 'Score',
            'member_id' => 'Member ID',
        ];
    }

    /**
     * 状态选项
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '待完成',
            self::STATUS_FINISHED => '已完成',
        ];
    }

    /**
     * 成绩详情
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetail()
    {
        return $this->hasMany(ScoreDetail::class, ['score_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->questions_count = 0;
                $this->last_answer_datetime = $this->end_datetime = time();
                $this->score = 0;
                $this->status = self::STATUS_PENDING;
                $this->member_id = Yii::$app->getUser()->getId() ?: 0;
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        ScoreDetail::deleteAll(['score_id' => $this->id]);
    }

}
