<?php

namespace app\modules\admin\modules\exam\models;

use yadjet\behaviors\ImageUploadBehavior;
use yii\db\Query;

/**
 * This is the model class for table "{{%exam_question_bank}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property integer $questions_count
 * @property integer $participation_times
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class QuestionBank extends \yii\db\ActiveRecord
{

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%exam_question_bank}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name', 'description'], 'trim'],
            [['questions_count', 'participation_times', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 60],
            [['description'], 'string', 'max' => 200],
            ['icon', 'image',
                'extensions' => 'png,jpeg',
                'minSize' => 1024,
                'maxSize' => 204800,
            ],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'icon',
                'thumb' => false
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '题库名称',
            'description' => '题库说明',
            'icon' => '题库图标',
            'questions_count' => '试题数量',
            'participation_times' => '参与次数',
            'status' => '状态',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
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
            self::STATUS_CLOSE => '关闭',
            self::STATUS_OPEN => '开放',
        ];
    }

    public static function getItems()
    {
        return (new Query())
            ->select('name')
            ->from('{{%question_bank}}')
            ->indexBy('id')
            ->column();
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->questions_count = $this->participation_times = 0;
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = \Yii::$app->getUser()->getId();
            } else {
                $this->updated_at = time();
                $this->updated_by = \Yii::$app->getUser()->getId();
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Question::deleteAll(['question_bank_id' => $this->id]);
    }

}
