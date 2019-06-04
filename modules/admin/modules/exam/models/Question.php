<?php

namespace app\modules\admin\modules\exam\models;

use Yii;

/**
 * This is the model class for table "{{%exam_question}}".
 *
 * @property integer $id
 * @property integer $question_bank_id
 * @property integer $type
 * @property integer $status
 * @property string $content
 * @property string $options
 * @property string $answer
 * @property string $resolve
 */
class Question extends \yii\db\ActiveRecord
{

    /**
     * 题型
     */
    const TYPE_SINGLE_CHOICE = 0;
    const TYPE_MULTIPLE_CHOICE = 1;
    const TYPE_TRUE_OR_FALSE = 2;

    /**
     * 状态
     */
    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%exam_question}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_bank_id', 'content', 'answer'], 'required'],
            [['question_bank_id', 'type', 'status'], 'integer'],
            [['content', 'options', 'answer', 'resolve'], 'string'],
            [['content', 'options', 'answer', 'resolve'], 'trim'],
            [['content'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_bank_id' => '所属题库',
            'type' => '试题类型',
            'status' => '试题状态',
            'content' => '试题题干',
            'options' => '选项',
            'answer' => '答案设置',
            'resolve' => '试题解析',
        ];
    }

    public static function typeOptions()
    {
        return [
            self::TYPE_SINGLE_CHOICE => '单选题',
            self::TYPE_MULTIPLE_CHOICE => '多选题',
            self::TYPE_TRUE_OR_FALSE => '判断题',
        ];
    }

    public static function statusOptions()
    {
        return [
            self::STATUS_CLOSE => '关闭',
            self::STATUS_OPEN => '开放',
        ];
    }

    /**
     * 所属题库
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(QuestionBank::class, ['id' => 'question_bank_id']);
    }

    // Events
    private $_question_bank_id = 0;

    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->_question_bank_id = $this->question_bank_id;
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $db = Yii::$app->getDb();
        if ($insert) {
            $db->createCommand('UPDATE {{%exam_question_bank}} SET [[questions_count]] = [[questions_count]] + 1 WHERE [[id]] = :id', [':id' => $this->question_bank_id])->execute();
        } elseif ($this->_question_bank_id && $this->_question_bank_id != $this->question_bank_id) {
            $db->createCommand('UPDATE {{%exam_question_bank}} SET [[questions_count]] = [[questions_count]] - 1 WHERE [[id]] = :id', [':id' => $this->_question_bank_id])->execute();
            $db->createCommand('UPDATE {{%exam_question_bank}} SET [[questions_count]] = [[questions_count]] + 1 WHERE [[id]] = :id', [':id' => $this->question_bank_id])->execute();
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->getDb()->createCommand('UPDATE {{%exam_question_bank}} SET [[questions_count]] = [[questions_count]] - 1 WHERE [[id]] = :id', [':id' => $this->question_bank_id])->execute();
    }

}
