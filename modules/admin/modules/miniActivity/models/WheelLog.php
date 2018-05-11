<?php

namespace app\modules\admin\modules\miniActivity\models;

use Yii;

/**
 * This is the model class for table "{{%mini_activity_wheel_log}}".
 *
 * @property int $id
 * @property int $wheel_id 大转盘 id
 * @property int $is_win 是否获奖
 * @property int $award_id 奖品选项
 * @property string $ip_address IP 地址
 * @property int $post_datetime 提交时间
 * @property int $member_id 会员
 * @property int $is_get 是否兑奖
 * @property string $get_password 兑奖密码
 * @property int $get_datetime 兑奖时间
 * @property string $remark 备注
 */
class WheelLog extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mini_activity_wheel_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wheel_id', 'ip_address', 'post_datetime'], 'required'],
            [['wheel_id', 'award_id', 'post_datetime', 'member_id', 'get_datetime'], 'integer'],
            [['remark'], 'string'],
            [['is_win', 'is_get'], 'string', 'max' => 1],
            [['ip_address'], 'string', 'max' => 15],
            [['get_password'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wheel_id' => '大转盘 id',
            'is_win' => '是否获奖',
            'award_id' => '奖品选项',
            'ip_address' => 'IP 地址',
            'post_datetime' => '提交时间',
            'member_id' => '会员',
            'is_get' => '是否兑奖',
            'get_password' => '兑奖密码',
            'get_datetime' => '兑奖时间',
            'remark' => '备注',
        ];
    }
}
