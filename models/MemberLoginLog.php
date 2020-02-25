<?php

namespace app\models;

/**
 * This is the model class for table "{{%member_login_log}}".
 *
 * @property int $id
 * @property int $member_id 会员
 * @property string $ip 登录 IP
 * @property int $login_at 登录时间
 * @property string $client_information 客户端信息
 */
class MemberLoginLog extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_login_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'ip', 'login_at', 'client_information'], 'required'],
            [['member_id', 'login_at'], 'integer'],
            [['ip'], 'string', 'max' => 39],
            [['client_information'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '会员',
            'ip' => '登录 IP',
            'login_at' => '登录时间',
            'client_information' => '客户端信息',
        ];
    }

    /**
     * 所属会员
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }

}
