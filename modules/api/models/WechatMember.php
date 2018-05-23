<?php

namespace app\modules\api\models;

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
    public function fields()
    {
        return [
            'id',
            'memberId' => 'member_id',
            'subscribe' => function () {
                return boolval($this->subscribe);
            },
            'openid',
            'nickname',
            'sex',
            'country',
            'province',
            'city',
            'language',
            'headimgurl',
            'subscribeTime' => 'subscribe_time',
            'unionid',
        ];
    }

}
