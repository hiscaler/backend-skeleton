<?php

namespace app\modules\api\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $username
 * @property string $nickname
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $tel
 * @property string $mobile_phone
 * @property integer $register_ip
 * @property integer $login_count
 * @property integer $last_login_ip
 * @property integer $last_login_time
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Member extends ActiveRecord
{

    /**
     * 用户状态
     */
    const STATUS_PENDING = 0; // 待审核状态
    const STATUS_ACTIVE = 1; // 激活状态
    const STATUS_LOCKED = 2; // 禁止状态
    const STATUS_DELETED = 3; // 删除状态

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'type',
            'username',
            'nickname',
            'avatar',
            'email',
            'tel',
            'mobile_phone',
            'registerIp' => 'register_ip',
            'loginCount' => 'login_count',
            'lastLoginIp' => 'last_login_ip',
            'lastLoginTime' => 'last_login_time',
            'status' => 'status',
            'remark' => 'remark',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }
}
