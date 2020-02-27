<?php

namespace app\modules\admin\modules\vote\models;

/**
 * This is the model class for table "{{%vote_log}}".
 *
 * @property int $id
 * @property int $vote_id 投票 id
 * @property string $option_id 投票选项
 * @property string $ip_address IP 地址
 * @property int $post_datetime 投票时间
 * @property int $member_id 会员
 */
class VoteLog extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vote_id', 'option_id', 'ip_address', 'post_datetime'], 'required'],
            [['vote_id', 'option_id', 'post_datetime', 'member_id'], 'integer'],
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
            'vote_id' => '投票 id',
            'option_id' => '投票选项',
            'ip_address' => 'IP 地址',
            'post_datetime' => '投票时间',
            'member_id' => '会员',
        ];
    }

}
