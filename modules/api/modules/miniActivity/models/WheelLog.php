<?php

namespace app\modules\api\modules\miniActivity\models;

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

    public function fields()
    {
        return [
            'id',
            'wheelTitle' => function () {
                return $this->wheel->title;
            },
            'awardName' => function () {
                return $this->award->title;
            },
            'postDatetime' => 'post_datetime',
        ];
    }

    /**
     * 大转盘
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWheel()
    {
        return $this->hasOne(Wheel::class, ['id' => 'wheel_id']);
    }

    /**
     * 奖品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAward()
    {
        return $this->hasOne(WheelAward::class, ['id' => 'award_id']);
    }

}
