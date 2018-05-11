<?php

namespace app\modules\admin\modules\miniActivity\models;

use yadjet\behaviors\ImageUploadBehavior;

/**
 * This is the model class for table "{{%mini_activity_wheel}}".
 *
 * @property int $id
 * @property string $title 活动名称
 * @property string $win_message 中奖消息
 * @property string $get_award_message 兑奖消息
 * @property int $begin_datetime 开始时间
 * @property int $end_datetime 结束时间
 * @property string $description 活动说明
 * @property string $photo 活动预览图片
 * @property string $repeat_play_message 重复抽奖提示信息
 * @property string $background_image 背景图片
 * @property string $background_image_repeat_type 背景类型
 * @property string $finished_title 活动结束公告主题
 * @property string $finished_description 活动结束说明
 * @property string $finished_photo 活动结束图片
 * @property int $estimated_people_count 预计活动人数
 * @property int $actual_people_count 实际活动人数
 * @property int $play_times_per_person 每人抽奖总次数
 * @property int $play_limit_type 抽奖限制规则
 * @property int $play_times_per_person_by_limit_type 抽奖限制规则次数
 * @property int $win_times_per_person 每人中奖次数
 * @property int $win_interval_seconds 每人每次中奖时间间隔
 * @property int $show_awards_quantity 显示奖品数量
 * @property int $blocks_count 区块数量
 * @property int $ordering 排序
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Wheel extends \yii\db\ActiveRecord
{

    const PLAY_LIMIT_TYPE_NO_LIMIT = 0;
    const PLAY_LIMIT_TYPE_IP = 1;
    const PLAY_LIMIT_TYPE_DAY = 2;
    const PLAY_LIMIT_TYPE_WEEK = 3;
    const PLAY_LIMIT_TYPE_MONTH = 4;
    const PLAY_LIMIT_TYPE_QUARTER = 5;
    const PLAY_LIMIT_TYPE_YEAR = 6;
    const PLAY_LIMIT_TYPE_WHOLE = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mini_activity_wheel}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'win_message', 'begin_datetime', 'end_datetime', 'repeat_play_message', 'finished_title', 'blocks_count'], 'required'],
            [['estimated_people_count', 'actual_people_count', 'play_times_per_person', 'play_limit_type', 'play_times_per_person_by_limit_type', 'win_times_per_person', 'win_interval_seconds', 'blocks_count', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['begin_datetime', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'begin_datetime'],
            ['end_datetime', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'end_datetime'],
            [['estimated_people_count', 'actual_people_count', 'win_interval_seconds'], 'default', 'value' => 0],
            [['play_times_per_person_by_limit_type', 'win_times_per_person', 'play_times_per_person'], 'default', 'value' => 1],
            [['play_limit_type'], 'default', 'value' => self::PLAY_LIMIT_TYPE_NO_LIMIT],
            [['description', 'finished_description'], 'string'],
            [['title', 'finished_title'], 'string', 'max' => 100],
            [['win_message', 'get_award_message', 'repeat_play_message'], 'string', 'max' => 255],
            [['background_image_repeat_type'], 'string', 'max' => 20],
            [['get_award_message', 'background_image_repeat_type'], 'default', 'value' => ''],
            [['show_awards_quantity', 'enabled'], 'boolean'],
            ['photo', 'image',
                'extensions' => 'jpg,jpeg,png',
                'minSize' => 1024,
                'maxSize' => 201800,
            ],
            ['finished_photo', 'image',
                'extensions' => 'jpg,jpeg,png',
                'minSize' => 1024,
                'maxSize' => 201800,
            ],
            ['background_image', 'image',
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
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'finished_photo',
                'thumb' => false
            ],
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'background_image',
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
            'title' => '活动名称',
            'win_message' => '中奖消息',
            'get_award_message' => '兑奖消息',
            'begin_datetime' => '开始时间',
            'end_datetime' => '结束时间',
            'description' => '活动说明',
            'photo' => '活动预览图片',
            'repeat_play_message' => '重复抽奖提示信息',
            'background_image' => '背景图片',
            'background_image_repeat_type' => '背景类型',
            'finished_title' => '活动结束公告主题',
            'finished_description' => '活动结束说明',
            'finished_photo' => '活动结束图片',
            'estimated_people_count' => '预计活动人数',
            'actual_people_count' => '实际活动人数',
            'play_times_per_person' => '每人抽奖总次数',
            'play_limit_type' => '抽奖限制规则',
            'play_times_per_person_by_limit_type' => '抽奖限制规则次数',
            'win_times_per_person' => '每人中奖次数',
            'win_interval_seconds' => '每人每次中奖时间间隔',
            'show_awards_quantity' => '显示奖品数量',
            'blocks_count' => '区块数量',
            'ordering' => '排序',
            'enabled' => '激活',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    /**
     * 抽奖限制规则
     *
     * @return array
     */
    public static function playLimitTypeOptions()
    {
        return [
            self::PLAY_LIMIT_TYPE_NO_LIMIT => '无限制',
            self::PLAY_LIMIT_TYPE_IP => 'IP 限制',
            self::PLAY_LIMIT_TYPE_DAY => '每日限制',
            self::PLAY_LIMIT_TYPE_WEEK => '每周限制',
            self::PLAY_LIMIT_TYPE_MONTH => '每月限制',
            self::PLAY_LIMIT_TYPE_QUARTER => '每季限制',
            self::PLAY_LIMIT_TYPE_YEAR => '每年限制',
            self::PLAY_LIMIT_TYPE_WHOLE => '整个活动',
        ];
    }

    /**
     * 奖品项目
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAwards()
    {
        return $this->hasMany(WheelAward::class, ['wheel_id' => 'id']);
    }

    /**
     * 日志
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(WheelLog::class, ['wheel_id' => 'id']);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
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

}
