<?php

namespace app\modules\api\models;

use app\models\Meta;
use app\modules\api\components\ApplicationHelper;
use Yii;
use yii\helpers\FileHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $category_id
 * @property string $group
 * @property string $username
 * @property string $nickname
 * @property string $real_name
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $email
 * @property string $tel
 * @property string $mobile_phone
 * @property string $address
 * @property string $register_ip
 * @property integer $total_credits
 * @property integer $available_credits
 * @property integer $login_count
 * @property string $last_login_ip
 * @property integer $last_login_time
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Member extends BaseActiveRecord implements IdentityInterface
{

    /**
     * 会员类型
     */
    const TYPE_MEMBER = 0;
    const TYPE_OTHER = 1;

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

    public function transactions()
    {
        return [
            self::SCENARIO_DELETE => self::OP_DELETE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'total_credits', 'available_credits', 'login_count', 'last_login_time', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['username'], 'required'],
            [['register_ip', 'last_login_ip'], 'string', 'max' => 39],
            [['group', 'username', 'nickname', 'real_name', 'tel', 'mobile_phone', 'address', 'email'], 'trim'],
            [['type'], 'default', 'value' => self::TYPE_MEMBER],
            [['total_credits', 'available_credits'], 'default', 'value' => 0],
            [['remark'], 'string'],
            [['group', 'username', 'nickname', 'real_name'], 'string', 'max' => 20],
            [['avatar'], 'string', 'max' => 200],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['email'], 'email'],
            [['tel'], 'string', 'max' => 30],
            [['mobile_phone'], 'string', 'max' => 35],
            [['address'], 'string', 'max' => 100],
            [['username'], 'unique'],
            [['access_token'], 'string'],
            [['access_token'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['status'], 'default', 'value' => ApplicationHelper::getConfigValue('member.register.status', self::STATUS_PENDING)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'type',
            'categoryId' => 'category_id',
            'group',
            'username',
            'nickname',
            'realName' => 'real_name',
            'avatar' => function () {
                $avatar = $this->avatar;
                if (!empty($avatar)) {
                    $addUrl = true;
                    foreach (['http', 'https', '//'] as $prefix) {
                        if (strncasecmp($avatar, $prefix, strlen($prefix)) === 0) {
                            $addUrl = false;
                            break;
                        }
                    }

                    if ($addUrl) {
                        $avatar = Yii::$app->getRequest()->hostInfo . $avatar;
                    }
                }

                return $avatar;
            },
            'email',
            'tel',
            'mobilePhone' => 'mobile_phone',
            'address',
            'registerIp' => 'register_ip',
            'totalCredits' => 'total_credits',
            'availableCredits' => 'available_credits',
            'loginCount' => 'login_count',
            'lastLoginIp' => 'last_login_ip',
            'lastLoginTime' => 'last_login_time',
            'status' => 'status',
            'remark' => 'remark',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
            'metaItems' => function () {
                $items = [];
                $objectName = strtr(static::tableName(), ['{{%' => '', '}}' => '']);
                $rawItems = \Yii::$app->getDb()->createCommand('SELECT [[m.key]], [[m.return_value_type]], [[string_value]], [[text_value]], [[integer_value]], [[decimal_value]] FROM {{%meta_value}} t LEFT JOIN {{%meta}} m ON [[t.meta_id]] = [[m.id]] WHERE [[t.object_id]] = :objectId AND [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[object_name]] = :objectName)', [':objectId' => $this->id, ':objectName' => $objectName])->queryAll();
                foreach ($rawItems as $item) {
                    $valueKey = Meta::parseReturnKey($item['return_value_type']);
                    $items[$item['key']] = $item[$valueKey];
                }

                return $items;
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /**
         * Token 格式
         * 1. token值
         * 2. token值.有效的时间戳
         * 3. 类型.token值.有效的时间戳
         */
        $member = static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
        if ($member) {
            if (stripos($token, '.') === false) {
                return $member; // 1. token值
            } else {
                $tokens = explode('.', $token);
                if (isset($tokens[2])) {
                    // 3. 类型.token值.有效的时间戳
                    list (, , $expire) = $tokens;
                } else {
                    // 2. token值.有效的时间戳
                    list (, $expire) = $tokens;
                }
                $accessTokenExpire = ApplicationHelper::getConfigValue('member.accessTokenExpire', 86400);
                $accessTokenExpire = (int) $accessTokenExpire ?: 86400;

                return ((int) $expire + $accessTokenExpire) > time() ? $member : null;
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @param null $type
     * @return static|null
     */
    public static function findByUsername($username, $type = null)
    {
        $condition = ['username' => $username, 'status' => self::STATUS_ACTIVE];
        if ($type !== null) {
            $condition['type'] = (int) $type;
        }

        return static::findOne($condition);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = ApplicationHelper::getConfigValue('user.passwordResetTokenExpire');

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '.' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @param string $prefix
     * @throws \yii\base\Exception
     */
    public function generateAccessToken($prefix = 'pp')
    {
        $this->access_token = $prefix . '.' . Yii::$app->getSecurity()->generateRandomString() . '.' . time();
    }

    public function removeAccessToken()
    {
        $this->access_token = null;
    }

    /**
     * 关联的微信会员资料
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWechat()
    {
        return $this->hasOne(WechatMember::class, ['member_id' => 'id']);
    }

    // Events

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->nickname)) {
                $nickname = $this->real_name;
                empty($nickname) && $nickname = $this->username;
                $this->nickname = $nickname;
            }
            $user = Yii::$app->getUser();
            if ($insert) {
                $this->generateAuthKey();
                $this->register_ip = Yii::$app->getRequest()->getUserIP();
                $this->created_by = $this->updated_by = $user->getIsGuest() ? 0 : $user->getId();
                $this->created_at = $this->updated_at = time();
            } else {
                $this->updated_at = time();
                if ($user->getIsGuest()) {
                    $this->updated_by = $user->getId();
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $avatar = $this->avatar;
        if ($avatar && !filter_var($avatar, FILTER_VALIDATE_URL)) {
            $avatar = Yii::getAlias('@webroot/' . ltrim($avatar, '/'));
            file_exists($avatar) && FileHelper::unlink($avatar);
        }

        // 清理相关数据
        $cmd = \Yii::$app->getDb()->createCommand();
        foreach (['member_credit_log', 'wechat_member'] as $table) {
            $cmd->delete("{{%$table}}", [
                'member_id' => $this->id
            ])->execute();
        }
    }

}
