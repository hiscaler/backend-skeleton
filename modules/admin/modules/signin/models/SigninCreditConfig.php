<?php

namespace app\modules\admin\modules\signin\models;

/**
 * This is the model class for table "{{%signin_credit_config}}".
 *
 * @property int $id
 * @property string $message 消息
 * @property int $credits 积分
 */
class SigninCreditConfig extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%signin_credit_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['credits'], 'integer'],
            [['message'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => '消息',
            'credits' => '积分',
        ];
    }
}
