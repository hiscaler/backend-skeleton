<?php

namespace app\models;

/**
 * This is the model class for table "{{%member_profile}}".
 *
 * @property int $member_id 会员
 * @property string $tel 电话号码
 * @property string $address 地址
 * @property string $zip_code 邮编
 * @property int $status 状态
 */
class BaseMemberProfile extends \yii\db\ActiveRecord
{

    /**
     * 状态
     */
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLE = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id', 'status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => array_keys(self::statusOptions())],
            [['tel'], 'string', 'max' => 30],
            [['address'], 'string', 'max' => 100],
            [['zip_code'], 'string', 'max' => 6],
            [['member_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'member_id' => '会员',
            'tel' => '电话号码',
            'address' => '地址',
            'zip_code' => '邮编',
            'status' => '状态',
        ];
    }

    /**
     * 状态选项
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_ACTIVE => '激活',
            self::STATUS_DISABLE => '禁止',
        ];
    }

}
