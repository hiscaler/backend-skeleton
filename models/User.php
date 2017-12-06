<?php

namespace app\models;

use app\modules\admin\components\ApplicationHelper;
use yadjet\behaviors\FileUploadBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "mai_user".
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
 * @property integer $role
 * @property integer $credits_count
 * @property string $user_group
 * @property string $system_group
 * @property integer $register_ip
 * @property integer $login_count
 * @property integer $last_login_ip
 * @property integer $last_login_time
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{

    use ActiveRecordHelperTrait;

    /**
     * 用户类型
     */
    const TYPE_USER = 0;
    const TYPE_MEMBER = 1;

    /**
     * 用户状态
     */
    const STATUS_PENDING = 0; // 待审核状态
    const STATUS_ACTIVE = 1; // 激活状态
    const STATUS_LOCKED = 2; // 禁止状态
    const STATUS_DELETED = 3; // 删除状态

    /**
     * 用户角色
     */
    const ROLE_ADMINISTRATOR = 1;
    const ROLE_USER = 2;

    private $_fileUploadConfig;

    public function init()
    {
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::className2Id(), 'avatar');
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'role', 'credits_count', 'register_ip', 'login_count', 'last_login_ip', 'last_login_time', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['username'], 'required'],
            ['username', 'match', 'pattern' => '/^[a-z0-9]+[a-z0-9-]+[a-z0-9]$/'],
            [['username', 'nickname', 'user_group', 'system_group'], 'string', 'max' => 20],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['credits_count'], 'default', 'value' => 0],
            [['username'], 'unique'],
            ['email', 'email'],
            [['password_reset_token'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['role', 'default', 'value' => self::ROLE_USER],
            ['status', 'in', 'range' => array_keys(self::statusOptions())],
            ['avatar', 'file',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
                'tooSmall' => Yii::t('app', 'The file "{file}" is too small. Its size cannot be smaller than {limit}.', [
                    'limit' => ApplicationHelper::friendlyFileSize($this->_fileUploadConfig['size']['min']),
                ]),
                'tooBig' => Yii::t('app', 'The file "{file}" is too big. Its size cannot exceed {limit}.', [
                    'limit' => ApplicationHelper::friendlyFileSize($this->_fileUploadConfig['size']['max']),
                ]),
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => FileUploadBehavior::className(),
                'attribute' => 'avatar'
            ],
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
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username, $type = null)
    {
        $condition = ['username' => $username, 'status' => self::STATUS_ACTIVE];
        if ($type !== null) {
            $condition['type'] = strtolower($type) == 'user' ? self::TYPE_USER : self::TYPE_MEMBER;
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
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

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
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function removeAccessToken()
    {
        $this->access_token = $this->access_token_expire_datetime = null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'nickname' => Yii::t('user', 'Nickname'),
            'avatar' => Yii::t('user', 'Avatar'),
            'email' => Yii::t('user', 'Email'),
            'enabled' => Yii::t('app', 'Enabled'),
            'credits_count' => Yii::t('user', 'Credits Count'),
            'user_group' => Yii::t('user', 'User Group'),
            'user_group_text' => Yii::t('user', 'User Group'),
            'system_group' => Yii::t('user', 'System Group'),
            'system_group_text' => Yii::t('user', 'System Group'),
            'status' => Yii::t('user', 'Status'),
            'status_text' => Yii::t('user', 'Status'),
            'register_ip' => Yii::t('user', 'Register IP'),
            'login_count' => Yii::t('user', 'Login Count'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'last_login_time' => Yii::t('user', 'Last Login Time'),
            'last_login_ip' => Yii::t('user', 'Last Login IP'),
        ];
    }

    public static function roleOptions()
    {
        return [
            self::ROLE_ADMINISTRATOR => '系统管理员',
            self::ROLE_USER => '普通用户',
        ];
    }

    /**
     * 用户状态选项
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_ACTIVE => '激活',
            self::STATUS_LOCKED => '锁定',
            self::STATUS_DELETED => '删除',
        ];
    }

    /**
     * 用户状态
     *
     * @return string|mixed
     */
    public function getStatus_text()
    {
        $options = self::statusOptions();

        return isset($options[$this->status]) ? $options[$this->status] : null;
    }

    /**
     * 用户分组
     *
     * @return string|mixed
     */
    public function getUser_group_text()
    {
        $options = UserGroup::userGroupOptions();

        return isset($options[$this->user_group]) ? $options[$this->user_group] : null;
    }

    /**
     * 系统分组
     *
     * @return string|mixed
     */
    public function getSystem_group_text()
    {
        $options = UserGroup::systemGroupOptions();

        return isset($options[$this->system_group]) ? $options[$this->system_group] : null;
    }

    /**
     * 根据用户积分修正用户所在分组
     *
     * @param integer $userId
     * @return boolean
     */
    public static function fixUserGroup($userId)
    {
        $db = Yii::$app->getDb();
        $userId = (int) $userId;
        $credits = $db->createCommand('SELECT [[credits_count]] FROM {{%user}} WHERE [[id]] = :id', [':id' => $userId])->queryScalar();
        if ($credits !== false) {
            $result = false;
            $groups = $db->createCommand('SELECT [[alias]], [[min_credits]], [[max_credits]] FROM {{%user_group}} WHERE [[max_credits]] >= :credits', [':credits' => $credits])->queryAll();
            foreach ($groups as $group) {
                if ($credits >= $group['min_credits'] && $credits <= $group['max_credits']) {
                    $db->createCommand()->update('{{%user}}', ['user_group' => $group['alias']], ['id' => $userId])->execute();
                    $result = true;
                    break;
                }
            }

            return $result;
        } else {
            return false;
        }
    }

    private static function _getList($where)
    {
        return (new \yii\db\Query())
            ->select('username')
            ->from(self::tableName())
            ->where($where)
            ->indexBy('id')
            ->column();
    }

    /**
     * 获取人员列表
     *
     * @return array
     */
    public static function getList()
    {
        return self::_getList(['type' => static::TYPE_MEMBER]);
    }

    public static function getListByUserGroup($group)
    {
        return self::_getList(['type' => static::TYPE_MEMBER, 'user_group' => trim($group)]);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->nickname)) {
                $this->nickname = $this->username;
            }
            if ($insert) {
                $this->generateAuthKey();
                $this->register_ip = Yii::$app->getRequest()->getUserIP();
                if ($this->type == self::TYPE_MEMBER) {
                    $this->created_by = $this->updated_by = 0;
                } else {
                    $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
                }
                $this->created_at = $this->updated_at = time();
            } else {
                $this->updated_at = time();
                $this->updated_by = Yii::$app->getUser()->getId();
            }

            return true;
        } else {
            return false;
        }
    }

}
