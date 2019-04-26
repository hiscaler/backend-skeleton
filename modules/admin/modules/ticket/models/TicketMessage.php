<?php

namespace app\modules\admin\modules\ticket\models;

use app\models\Member;
use app\models\User;

/**
 * This is the model class for table "{{%ticket_message}}".
 *
 * @property int $id
 * @property int $ticket_id 工单
 * @property int $type 类型
 * @property string $content 消息
 * @property int $parent_id 引用消息
 * @property int $member_id 会员
 * @property int $reply_user_id 回复人
 * @property string $reply_username 回复人
 * @property int $created_at 添加时间
 */
class TicketMessage extends \yii\db\ActiveRecord
{

    /**
     * 类型
     */
    const TYPE_MEMBER = 0;
    const TYPE_CUSTOMER_SERVICE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket_message}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'type', 'content'], 'required'],
            [['ticket_id', 'type', 'parent_id', 'member_id', 'reply_user_id', 'created_at'], 'integer'],
            ['type', 'default', 'value' => self::TYPE_CUSTOMER_SERVICE],
            ['type', 'in', 'range' => array_keys(self::typeOptions())],
            ['content', 'trim'],
            [['content'], 'string'],
            [['reply_username'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'ticket_id' => '工单',
            'type' => '类型',
            'content' => '消息',
            'parent_id' => '引用消息',
            'member_id' => '会员',
            'member.username' => '会员',
            'reply_user_id' => '回复人',
            'reply_username' => '回复人',
            'created_at' => '回复时间',
        ];
    }

    /**
     * 类型选项
     *
     * @return array
     */
    public static function typeOptions()
    {
        return [
            self::TYPE_MEMBER => '会员',
            self::TYPE_CUSTOMER_SERVICE => '客服',
        ];
    }

    /**
     * 所属工单
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticket_id']);
    }

    /**
     * 会员
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id'])
            ->select(['id', 'username']);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                /* @var $user User */
                $user = \Yii::$app->getUser()->getIdentity();
                if ($this->type == self::TYPE_MEMBER) {
                    $this->member_id = $user->getId();
                } else {
                    $this->reply_user_id = $user->getId();
                    $this->reply_username = $user->getUsername();
                }

                $this->created_at = time();
            }

            return true;
        } else {
            return false;
        }
    }

}
