<?php

namespace app\modules\admin\modules\feedback\models;

use Yii;

/**
 * This is the model class for table "{{%feedback}}".
 *
 * @property int $id
 * @property int $category_id 分类
 * @property string $title 标题
 * @property string $username 姓名
 * @property string $tel 电话号码
 * @property string $mobile_phone 手机号码
 * @property string $email 邮箱
 * @property string $message 内容
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Feedback extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feedback}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['message'], 'required'],
            ['category_id', 'default', 'value' => 0],
            [['title', 'tel', 'mobile_phone', 'email'], 'trim'],
            ['email', 'email'],
            [['message'], 'string'],
            [['title'], 'string', 'max' => 100],
            [['username', 'tel'], 'string', 'max' => 20],
            [['mobile_phone'], 'string', 'max' => 11],
            [['email'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => '分类',
            'title' => '标题',
            'username' => '姓名',
            'tel' => '电话号码',
            'mobile_phone' => '手机号码',
            'email' => '邮箱',
            'message' => '内容',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    // Events
    public function afterSave($insert, $changedAttributes)
    {
        if (parent::afterSave($insert, $changedAttributes)) {
            $userId = \Yii::$app->getUser()->getIsGuest() ? 0 : \Yii::$app->getUser()->getId();
            if ($insert) {
                $this->created_by = $this->updated_by = $userId;
                $this->created_at = $this->updated_at = time();
            } else {
                $this->updated_by = $userId;
                $this->updated_at = time();
            }

            return true;
        } else {
            return false;
        }
    }
}
