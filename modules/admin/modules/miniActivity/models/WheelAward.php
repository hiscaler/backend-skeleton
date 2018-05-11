<?php

namespace app\modules\admin\modules\miniActivity\models;

use app\modules\api\models\Constant;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;

/**
 * This is the model class for table "{{%mini_activity_wheel_award}}".
 *
 * @property int $id
 * @property int $wheel_id 转盘 id
 * @property int $ordering 排序
 * @property string $title 名称
 * @property string $description 描述
 * @property string $photo 奖品图片
 * @property int $total_quantity 总奖品数量
 * @property int $remaining_quantity 剩余奖品数量
 * @property int $enabled 激活
 */
class WheelAward extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mini_activity_wheel_award}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wheel_id', 'title'], 'required'],
            [['wheel_id', 'ordering', 'total_quantity', 'remaining_quantity'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 60],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => Constant::BOOLEAN_TRUE],
            [['total_quantity', 'remaining_quantity'], 'default', 'value' => 0],
            ['ordering', 'unique', 'targetAttribute' => ['wheel_id', 'ordering'], 'message' => '同一个转盘中排序必须唯一。'],
            ['photo', 'image',
                'extensions' => 'jpg,jpeg,png',
                'minSize' => 1024,
                'maxSize' => 201800,
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'photo',
                'thumb' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wheel_id' => '转盘 id',
            'ordering' => '排序',
            'title' => '名称',
            'description' => '描述',
            'photo' => '奖品图片',
            'total_quantity' => '总奖品数量',
            'remaining_quantity' => '剩余奖品数量',
            'enabled' => '激活',
        ];
    }

    /**
     * 所属转盘
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWheel()
    {
        return $this->hasOne(Wheel::class, ['id' => 'wheel_id']);
    }

}
