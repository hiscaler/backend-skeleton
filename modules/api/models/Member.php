<?php

namespace app\modules\api\models;

use app\models\Meta;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property integer $type
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
 * @property integer $register_ip
 * @property integer $total_credits
 * @property integer $available_credits
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
class Member extends \yii\db\ActiveRecord implements IdentityInterface
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
    public function rules()
    {
        return [
            [['type', 'register_ip', 'total_credits', 'available_credits', 'login_count', 'last_login_ip', 'last_login_time', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['username'], 'required'],
            [['username', 'nickname', 'real_name', 'tel', 'mobile_phone', 'email'], 'trim'],
            [['type'], 'default', 'value' => 0],
            [['total_credits', 'available_credits'], 'default', 'value' => 0],
            [['remark'], 'string'],
            [['username', 'nickname', 'real_name'], 'string', 'max' => 20],
            [['avatar'], 'string', 'max' => 200],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['tel'], 'string', 'max' => 30],
            [['mobile_phone'], 'string', 'max' => 35],
            [['username'], 'unique'],
            [['access_token'], 'string'],
            [['access_token'], 'unique'],
            [['password_reset_token'], 'unique'],
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
            'username',
            'nickname',
            'realName' => 'real_name',
            'avatar',
            'email',
            'tel',
            'mobilePhone' => 'mobile_phone',
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
            'metas' => function () {
                $metas = [];
                $objectName = strtr(static::tableName(), ['{{%' => '', '}}' => '']);
                $items = \Yii::$app->getDb()->createCommand('SELECT [[m.key]], [[t.value]] FROM {{%meta_value}} t LEFT JOIN {{%meta}} m ON [[t.meta_id]] = [[m.id]] WHERE [[t.object_id]] = :objectId AND [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[object_name]] = :objectName)', [':objectId' => $this->id, ':objectName' => $objectName])->queryAll();
                foreach ($items as $item) {
                    $metas[$item['key']] = $item['value'];
                }

                return $metas;
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
        if ($type == 'app\modules\api\extensions\yii\filters\auth\AccessTokenAuth') {
            $tokens = explode('.', $token);
            if (count($tokens) == 3) {
                $tokenType = strtolower($tokens[0]);
                $tokenValue = $tokens[1];
                $tokenExpireDate = $tokens[2];
            } else {
                $tokenType = $tokenExpireDate = null;
                $tokenValue = $token;
            }
            switch ($tokenType) {
                case 'wxapp':
                    break;

                case 'wechat':
                    break;

                default:
                    break;
            }

            return static::findOne(['access_token' => $token]);
        } else {
            $user = static::findOne(['access_token' => $token]);
            if ($user) {
                return (int) substr($token, strrpos($token, '.') + 1) > time() ? $user : null;
            } else {
                return null;
            }
        }
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
        $expire = Yii::$app->params['user . passwordResetTokenExpire'];

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
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
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
                if (Yii::$app->getUser()->isGuest) {
                    $this->created_by = $this->updated_by = 0;
                } else {
                    $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
                }
                $this->created_at = $this->updated_at = time();
            } else {
                $this->updated_at = time();
                if (!Yii::$app->getUser()->isGuest) {
                    $this->updated_by = Yii::$app->getUser()->getId();
                }
            }

            return true;
        } else {
            return false;
        }
    }

}
