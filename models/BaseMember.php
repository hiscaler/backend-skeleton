<?php

namespace app\models;

use app\helpers\Config;
use yadjet\behaviors\ImageUploadBehavior;
use yadjet\validators\MobilePhoneNumberValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $category_id
 * @property string $group
 * @property string $invitation_code
 * @property integer $parent_id
 * @property string $username
 * @property string $nickname
 * @property string $real_name
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $email
 * @property string $mobile_phone
 * @property string $register_ip
 * @property integer $total_money
 * @property integer $available_money
 * @property integer $total_credits
 * @property integer $available_credits
 * @property integer $alarm_credits
 * @property integer $login_count
 * @property string $last_login_ip
 * @property integer $last_login_time
 * @property string $last_login_session
 * @property integer $expired_datetime
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class BaseMember extends \yii\db\ActiveRecord implements IdentityInterface
{

    /**
     * 会员类型
     */
    const TYPE_NONE = 0;
    const TYPE_ADMINISTRATOR = 1;

    /**
     * 用户状态
     */
    const STATUS_PENDING = 0; // 待审核状态
    const STATUS_ACTIVE = 1; // 激活状态
    const STATUS_LOCKED = 2; // 锁定状态
    const STATUS_DELETED = 3; // 删除状态

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'avatar';

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
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['type', 'category_id', 'parent_id', 'total_money', 'available_money', 'total_credits', 'available_credits', 'alarm_credits', 'login_count', 'last_login_time', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['alarm_credits', 'default', 'value' => 0],
            ['expired_datetime', 'datetime', 'timestampAttribute' => 'expired_datetime'],
            'registerByUsername' => [['username'], 'required'],
            [['group', 'invitation_code', 'username', 'nickname', 'real_name', 'mobile_phone', 'email', 'remark'], 'trim'],
            ['invitation_code', 'string', 'max' => 16],
            [['register_ip', 'last_login_ip'], 'string', 'max' => 39],
            [['type'], 'default', 'value' => self::TYPE_NONE],
            [['type'], 'in', 'range' => array_keys(static::typeOptions())],
            [['category_id', 'parent_id'], 'default', 'value' => 0],
            [['remark'], 'string'],
            [['group', 'username', 'real_name'], 'string', 'max' => 20],
            [['nickname'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['email'], 'email'],
            [['mobile_phone'], 'string', 'max' => 35],
            ['mobile_phone', MobilePhoneNumberValidator::class],
            [['username'], 'unique'],
            [['access_token'], 'string'],
            [['access_token'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['last_login_session'], 'string', 'max' => 128],
            [['status'], 'default', 'value' => Config::get('member.register.status', self::STATUS_PENDING)],
            ['avatar', 'image',
                'extensions' => 'jpg,gif,png,jpeg',
                'minSize' => 1024,
                'maxSize' => 1024 * 200,
            ],
        ];

        // 自定义验证规则
        $requiredFields = Config::get('member.register.rules.required');
        if ($requiredFields && is_array($requiredFields)) {
            $rules = array_merge($rules, [[$requiredFields, 'required']]);
        }

        $uniqueFields = Config::get('member.register.rules.unique');
        if ($uniqueFields && is_array($uniqueFields)) {
            $rules = array_merge($rules, [[$uniqueFields, 'unique']]);
        }

        return $rules;
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'avatar'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '会员类型',
            'category_id' => '分类',
            'group' => '分组',
            'invitation_code' => '邀请码',
            'parent_id' => '上级',
            'parent.username' => '上级',
            'username' => '帐号',
            'nickname' => '昵称',
            'real_name' => '姓名',
            'avatar' => '头像',
            'auth_key' => '认证 key',
            'password_hash' => '密码',
            'password_reset_token' => '密码重置 token',
            'email' => '邮箱',
            'mobile_phone' => '手机号码',
            'register_ip' => '注册 IP',
            'total_money' => '总金额',
            'available_money' => '可用金额',
            'total_credits' => '总积分',
            'available_credits' => '可用积分',
            'alarm_credits' => '积分预警值',
            'login_count' => '登录次数',
            'last_login_ip' => '最后登录 IP',
            'last_login_time' => '最后登录时间',
            'expired_datetime' => '有效期',
            'status' => '状态',
            'remark' => '备注',
            'created_at' => '注册时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    /**
     * 验证会员是否到期
     *
     * @param BaseMember $member
     * @return BaseMember|null
     */
    private static function parseExpiredMember($member)
    {
        if ($member &&
            $member->expired_datetime &&
            Config::get('member.login.expiredAfter') != 'continue' &&
            $member->expired_datetime <= time()
        ) {
            $member = null;
        }

        return $member;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $member = static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);

        return self::parseExpiredMember($member);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $member = null;
        if ($type == 'app\modules\api\extensions\yii\filters\auth\AccessTokenAuth') {
            /**
             * Token 格式
             * 1. token值
             * 2. token值.有效的时间戳
             * 3. 类型.token值.有效的时间戳
             */
            $member = static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
            if ($member && stripos($token, '.') !== false) {
                $tokens = explode('.', $token);
                if (isset($tokens[2])) {
                    // 3. 类型.token值.有效的时间戳
                    list (, , $expire) = $tokens;
                } else {
                    // 2. token值.有效的时间戳
                    list (, $expire) = $tokens;
                }
                $accessTokenExpire = Config::get('member.accessTokenExpire', 86400);
                $accessTokenExpire = (int) $accessTokenExpire ?: 86400;

                if (((int) $expire + $accessTokenExpire) <= time()) {
                    $member = null;
                }
            }
        } else {
            $member = static::findOne(['access_token' => $token]);
            if ($member
                && ($index = strrpos($token, '.')) !== false
                && (int) substr($token, $index + 1) < time()) {
                $member = null;
            }
        }

        return self::parseExpiredMember($member);
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

        return self::parseExpiredMember(static::findOne($condition));
    }

    /**
     * 根据手机号码查找会员
     *
     * @param $mobilePhone
     * @param null $type
     * @return BaseMember|null
     */
    public static function findByMobilePhone($mobilePhone, $type = null)
    {
        $condition = ['mobile_phone' => $mobilePhone, 'status' => self::STATUS_ACTIVE];
        if ($type !== null) {
            $condition['type'] = (int) $type;
        }

        return self::parseExpiredMember(static::findOne($condition));
    }

    /**
     * Finds user by wechat openid
     *
     * @param $openid
     * @return static|null
     * @throws \yii\db\Exception
     */
    public static function findByWechatOpenId($openid)
    {
        $memberId = \Yii::$app->getDb()->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
        if ($memberId) {
            return static::findIdentity($memberId);
        } else {
            return null;
        }
    }

    /**
     * Finds user by wechat unionid
     *
     * @param $unionid
     * @return static|null
     * @throws \yii\db\Exception
     */
    public static function findByWechatUnionId($unionid)
    {
        $memberId = \Yii::$app->getDb()->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[unionid]] = :unionid', [':unionid' => $unionid])->queryScalar();
        if ($memberId) {
            return static::findIdentity($memberId);
        } else {
            return null;
        }
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
        $expire = Config::get('user.passwordResetTokenExpire');

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
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * 移除 access_token 值
     */
    public function removeAccessToken()
    {
        $this->access_token = null;
    }

    /**
     * 保存会员相关属性
     *
     * @param MemberProfile $profile
     * @return boolean
     */
    public function saveProfile($profile)
    {
        $profile->member_id = $this->id;

        return $profile->save();
    }

    /**
     * 会员类型选项
     *
     * @return array
     */
    public static function typeOptions()
    {
        $options = [
            self::TYPE_NONE => '普通会员',
        ];

        if (\Yii::$app->getUser()->identityClass == Member::class) {
            $options[self::TYPE_ADMINISTRATOR] = '管理员';
        }

        $types = Config::get('member.types', []);
        if ($types && ArrayHelper::isIndexed($types)) {
            foreach ($types as $key => $value) {
                if ($key == self::TYPE_ADMINISTRATOR) {
                    unset($types[$key]);
                }
            }
            $types && $options = array_replace($options, $types);
        }

        return $options;
    }

    /**
     * 会员状态选项
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
     * 会员列表
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public static function map()
    {
        $members = [];
        $rawMembers = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[username]] FROM {{%member}}')->queryAll();
        foreach ($rawMembers as $member) {
            $members[$member['id']] = $member['username'];
        }

        return $members;
    }

    /**
     * 上级
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Member::class, ['id' => 'parent_id']);
    }

    /**
     * 微信资料
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWechat()
    {
        return $this->hasOne(WechatMember::class, ['member_id' => 'id']);
    }

    /**
     * 会员资料
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(MemberProfile::class, ['member_id' => 'id']);
    }

    /**
     * 分类
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * 积分记录
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreditLogs()
    {
        return $this->hasMany(MemberCreditLog::class, ['member_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
    }

    /**
     * 根据用户积分修正用户所在分组
     *
     * @param integer $memberId
     * @return boolean
     * @throws \yii\db\Exception
     */
    public static function updateGroup($memberId)
    {
        $db = Yii::$app->getDb();
        $memberId = (int) $memberId;
        $credits = $db->createCommand('SELECT [[available_credits]] FROM {{%member}} WHERE [[id]] = :id', [':id' => $memberId])->queryScalar();
        if ($credits !== false) {
            $success = false;
            $groups = $db->createCommand('SELECT [[alias]], [[min_credits]], [[max_credits]] FROM {{%member_group}} WHERE [[max_credits]] >= :credits', [':credits' => $credits])->queryAll();
            foreach ($groups as $group) {
                if ($credits >= $group['min_credits'] && $credits <= $group['max_credits']) {
                    $db->createCommand()->update('{{%member}}', ['group' => $group['alias']], ['id' => $memberId])->execute();
                    $success = true;
                    break;
                }
            }

            return $success;
        } else {
            return false;
        }
    }

    /**
     * 会员登录善后处理
     *
     * @param $event
     * @throws \yii\db\Exception
     */
    public static function afterLogin($event)
    {
        $user = \Yii::$app->getUser();
        if (!$user->getIsGuest()) {
            \Yii::$app->getDb()->createCommand('UPDATE {{%member}} SET [[login_count]] = [[login_count]] + 1, [[last_login_ip]] = :loginIp, [[last_login_time]] = :loginTime, [[last_login_session]] = :lastLoginSession WHERE [[id]] = :id', [
                ':loginIp' => Yii::$app->getRequest()->getUserIP(),
                ':loginTime' => time(),
                ':lastLoginSession' => session_id(),
                ':id' => $user->getId()
            ])->execute();
        }
    }

    // Events

    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->nickname)) {
                $nickname = $this->real_name;
                empty($nickname) && $nickname = $this->username;
                $this->nickname = $nickname;
            }
            if ($insert) {
                $this->total_money = $this->available_money = 0;
                $this->generateAuthKey();
                $this->generateAccessToken();
                $this->status = Config::get('member.register.status', self::STATUS_PENDING);
                // @todo 需要检测唯一性
                $this->invitation_code = \yadjet\helpers\StringHelper::generateRandomString(16);
                $this->register_ip = Yii::$app->getRequest()->getUserIP();
                if (!$this->expired_datetime) {
                    $expiryMinutes = (int) Config::get('member.register.expiryMinutes');
                    if ($expiryMinutes) {
                        $this->expired_datetime = time() + $expiryMinutes * 60;
                    }
                }
                if (Yii::$app->getUser()->isGuest) {
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
        $tables = ['member_credit_log', 'wechat_member', 'member_profile'];
        foreach ($tables as $table) {
            $cmd->delete("{{%$table}}", ['member_id' => $this->id])->execute();
        }
    }

}
