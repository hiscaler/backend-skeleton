<?php

namespace app\modules\admin\modules\signin\models;

use Yii;

/**
 * This is the model class for table "{{%signin}}".
 *
 * @property int $id
 * @property int $member_id 会员 id
 * @property int $ymd 签到年月日
 * @property int $signin_datetime 签到时间
 * @property int $credits　积分
 * @property string $ip_address IP
 */
class Signin extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%signin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'ymd', 'signin_datetime', 'ip_address'], 'required'],
            [['member_id', 'ymd', 'signin_datetime', 'credits'], 'integer'],
            ['credits', 'default', 'value' => 0],
            [['ip_address'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员 id',
            'ymd' => '签到年月日',
            'signin_datetime' => '签到时间',
            'credits' => '积分',
            'ip_address' => 'IP',
        ];
    }

}
