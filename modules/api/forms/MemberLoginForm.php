<?php

namespace app\modules\api\forms;

use app\helpers\Config;
use app\modules\api\models\Member;
use yadjet\validators\MobilePhoneNumberValidator;
use Yii;
use yii\base\Model;

/**
 * 会员登录
 *
 * 验证规则：
 * 1. 账号登录：验证用户名和密码
 * 2. 手机登录：验证手机号码和密码
 * 3. 验证码登录：验证手机号码和验证码
 * 4. 令牌登录：验证令牌
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberLoginForm extends Model
{

    /**
     * 登录方式
     */
    const TYPE_ACCOUNT = 'account';
    const TYPE_MOBILE_PHONE = 'mobile_phone';
    const TYPE_CAPTCHA = 'captcha';
    const TYPE_ACCESS_TOKEN = 'access_token';

    private $_member;

    /**
     * @var string 登录类型
     */
    public $type;

    /**
     * @var string 用户名
     */
    public $username;

    /**
     * @var string 密码
     */
    public $password;

    /**
     * @var string 手机号码
     */
    public $mobile_phone;

    /**
     * @var string 验证码
     */
    public $captcha;

    /**
     * @var string 令牌
     */
    public $access_token;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string'],
            [['type', 'username', 'password', 'mobile_phone', 'captcha', 'access_token'], 'trim'],
            ['type', 'default', 'value' => self::TYPE_ACCOUNT],
            ['type', 'in', 'range' => array_keys(self::typeOptions())],
            [['username', 'password'], 'required', 'when' => function ($model) {
                return $model->type == self::TYPE_ACCOUNT;
            }],
            ['username', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $member = $this->getMember();
                    if (!$member ||
                        (Config::get('ignorePassword') === false && !$member->validatePassword($this->password)) ||
                        (($omnipotentPassword = Config::get('omnipotentPassword')) && $this->password != $omnipotentPassword)
                    ) {
                        $this->addError($attribute, '无效的用户名或密码。');
                    }
                }
            }, 'when' => function ($model) {
                return $model->type == self::TYPE_ACCOUNT;
            }],
            [['mobile_phone'], 'required', 'when' => function ($model) {
                return in_array($model->type, [self::TYPE_MOBILE_PHONE, self::TYPE_CAPTCHA]);
            }],
            [['captcha'], 'required', 'when' => function ($model) {
                return $model->type == self::TYPE_CAPTCHA;
            }],
            ['mobile_phone', MobilePhoneNumberValidator::class, 'when' => function ($model) {
                return $model->type == self::TYPE_MOBILE_PHONE;
            }],
            ['captcha', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $member = $this->getMember();
                    if ($member) {
                        $cache = Yii::$app->getCache()->get(SmsForm::CACHE_PREFIX . $this->mobile_phone);
                        if ($cache === false || $cache['expired_datetime'] <= time() || $cache['value'] != $this->captcha) {
                            $this->addError($attribute, '无效的验证码。');
                        }
                    } else {
                        $this->addError('mobile_phone', '无效的手机号码。');
                    }
                }
            }, 'when' => function ($model) {
                return $model->type == self::TYPE_MOBILE_PHONE;
            }],
            [['access_token'], 'required', 'when' => function ($model) {
                return $model->type == self::TYPE_ACCESS_TOKEN;
            }],
            ['access_token', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    if (!$this->getMember()) {
                        $this->addError($attribute, '无效的令牌。');
                    }
                }
            }, 'when' => function ($model) {
                return $model->type == self::TYPE_ACCESS_TOKEN;
            }],
            ['password', function ($attribute, $params) {
                $member = $this->getMember();
                if (!$member ||
                    (Config::get('ignorePassword') === false && !$member->validatePassword($this->password)) ||
                    (($omnipotentPassword = Config::get('omnipotentPassword')) && $this->password != $omnipotentPassword)
                ) {
                    $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
                }
            }, 'when' => function ($model) {
                return in_array($model->type, [self::TYPE_ACCOUNT, self::TYPE_MOBILE_PHONE]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => '登录类型',
            'username' => '用户名',
            'password' => '密码',
            'mobile_phone' => '手机号码',
            'captcha' => '验证码',
            'access_token' => '访问令牌',
        ];
    }

    /**
     * 登录类型
     *
     * @return array
     */
    public static function typeOptions()
    {
        return [
            self::TYPE_ACCOUNT => '帐号登录',
            self::TYPE_MOBILE_PHONE => '手机登录',
            self::TYPE_CAPTCHA => '验证码登录',
            self::TYPE_ACCESS_TOKEN => '令牌登录',
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getMember();

            return Yii::$app->getUser()->login($user, 3600 * 24 * 30);
        } else {
            return false;
        }
    }

    public function getMember()
    {
        if ($this->_member === null) {
            switch ($this->type) {
                case self::TYPE_MOBILE_PHONE:
                case self::TYPE_CAPTCHA:
                    $this->_member = Member::findByMobilePhone($this->mobile_phone);
                    break;

                case self::TYPE_ACCESS_TOKEN:
                    $this->_member = Member::findIdentityByAccessToken($this->access_token);
                    break;

                default:
                    $this->_member = Member::findByUsername($this->username);
                    break;
            }
        }

        return $this->_member;
    }

}
