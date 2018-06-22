<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%wechat_member}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $subscribe
 * @property string $openid
 * @property string $nickname
 * @property integer $sex
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $language
 * @property string $headimgurl
 * @property integer $subscribe_time
 * @property string $unionid
 */
class WechatMember extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_member}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'subscribe', 'sex', 'subscribe_time'], 'integer'],
            [['openid', 'nickname'], 'required'],
            [['openid'], 'string', 'max' => 28],
            [['country', 'province', 'city', 'language'], 'string', 'max' => 50],
            [['nickname'], 'string', 'max' => 60],
            [['headimgurl'], 'string', 'max' => 200],
            [['unionid'], 'string', 'max' => 29],
            [['openid'], 'unique'],
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
            'subscribe' => '是否关注',
            'openid' => 'openid',
            'nickname' => '昵称',
            'sex' => '性别',
            'country' => '国家',
            'province' => '省份',
            'city' => '城市',
            'language' => '语言',
            'headimgurl' => '头像',
            'subscribe_time' => '关注时间',
            'unionid' => 'unionid',
        ];
    }
}
