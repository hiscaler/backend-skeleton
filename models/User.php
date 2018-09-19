<?php

namespace app\models;

use app\modules\admin\components\ApplicationHelper;
use yadjet\behaviors\FileUploadBehavior;
use yadjet\helpers\UtilHelper;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $nickname
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $role
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
     * 用户状态
     */
    const STATUS_LOCKED = 0; // 锁定状态
    const STATUS_ACTIVE = 1; // 激活状态

    private $_fileUploadConfig;

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'avatar';

    /**
     * @throws \yii\db\Exception
     */
    public function init()
    {
        parent::init();
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::class, 'avatar');
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
            [['register_ip', 'login_count', 'last_login_ip', 'last_login_time', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['username'], 'required'],
            ['username', 'match', 'pattern' => '/^[a-z0-9]+[a-z0-9-]+[a-z0-9]$/'],
            [['username', 'nickname'], 'string', 'max' => 20],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['username'], 'unique'],
            ['email', 'email'],
            [['password_reset_token'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['role', 'string', 'min' => 1, 'max' => 64],
            ['status', 'in', 'range' => array_keys(self::statusOptions())],
            ['avatar', 'file',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => FileUploadBehavior::class,
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
     * @throws NotSupportedException
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
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
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
     * 用户名
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * 用户角色
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
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
            'role' => Yii::t('user', 'Role'),
            'enabled' => Yii::t('app', 'Enabled'),
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

    /**
     * 用户角色选项
     *
     * @return array
     */
    public static function roleOptions()
    {
        $roles = [];
        $authManager = Yii::$app->getAuthManager();
        if ($authManager) {
            foreach ($authManager->getRoles() as $role) {
                $roles[$role->name] = $role->description ?: $role->name;
            }
        }

        return $roles;
    }

    /**
     * 用户状态选项
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_ACTIVE => '激活',
            self::STATUS_LOCKED => '锁定',
        ];
    }

    /**
     * 人员列表
     *
     * @param null $role
     * @return array
     */
    public static function map($role = null)
    {
        $where = [];
        if ($role !== null) {
            $where['role'] = $role;
        }

        return (new Query())
            ->select('nickname')
            ->from('{{%user}}')
            ->where($where)
            ->indexBy('id')
            ->column();
    }

    /**
     * 用户登录善后处理
     *
     * @param $event
     * @throws \yii\db\Exception
     */
    public static function afterLogin($event)
    {
        $ip = ip2long(Yii::$app->getRequest()->getUserIP()) ?: 0;
        $now = time();
        $userId = \Yii::$app->getUser()->getId();
        $db = \Yii::$app->getDb();
        $db->createCommand('UPDATE {{%user}} SET [[login_count]] = [[login_count]] + 1, [[last_login_ip]] = :loginIp, [[last_login_time]] = :loginTime WHERE [[id]] = :id', [
            ':loginIp' => $ip,
            ':loginTime' => $now,
            ':id' => $userId
        ])->execute();
        $db->createCommand()->insert('{{%user_login_log}}', [
            'user_id' => $userId,
            'login_ip' => $ip,
            'client_information' => UtilHelper::getBrowserName(),
            'login_at' => $now,
        ])->execute();
    }

    // Events
    private $_roleName = null;

    public function afterFind()
    {
        parent::afterFind();
        if (!$this->getIsNewRecord()) {
            $this->_roleName = $this->role;
        }
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->nickname)) {
                $this->nickname = $this->username;
            }
            if ($insert) {
                $this->generateAuthKey();
                $this->register_ip = Yii::$app->getRequest()->getUserIP();
                $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
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

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $authManager = Yii::$app->getAuthManager();
        if ($authManager) {
            $roleName = $this->role;
            $role = !empty($roleName) ? $authManager->getRole($roleName) : null;
            if ($role) {
                if ($insert) {
                    $authManager->assign($role, $this->id);
                } else {
                    if ($this->_roleName != $this->role) {
                        if (!empty($this->_roleName)) {
                            $oldRole = $authManager->getRole($this->_roleName);
                            if ($oldRole) {
                                $authManager->revoke($oldRole, $this->id);
                            }
                        }
                        $roles = $authManager->getRolesByUser($this->id);
                        $roles && !in_array($role, $roles) && $authManager->assign($role, $this->id);
                    }
                }
            } elseif (!$insert) {
                if (!empty($this->_roleName)) {
                    if ($role = $authManager->getRole($this->_roleName)) {
                        $authManager->revoke($role, $this->id);
                    }
                }
            }
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        if ($authManager = Yii::$app->getAuthManager()) {
            $authManager->revokeAll($this->id);
        }

        // 删除关联数据
        $cmd = \Yii::$app->getDb()->createCommand();
        $tables = ['user_auth_category', 'grid_column_config', 'user_login_log'];
        foreach ($tables as $table) {
            $cmd->delete("{{%$table}}", ['user_id' => $this->id])->execute();
        }
    }

}
