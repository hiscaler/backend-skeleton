<?php

namespace app\modules\admin\modules\notice\models;

/**
 * This is the model class for table "{{%notice_view}}".
 *
 * @property int $id
 * @property int $notice_id 通知
 * @property int $member_id 会员
 * @property int $view_datetime 查看时间
 */
class NoticeView extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notice_view}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notice_id', 'member_id', 'view_datetime'], 'required'],
            [['notice_id', 'member_id', 'view_datetime'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notice_id' => '通知',
            'member_id' => '会员',
            'view_datetime' => '查看时间',
        ];
    }

}
