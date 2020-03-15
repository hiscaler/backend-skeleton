<?php

namespace app\modules\api\forms;

use app\helpers\Config;
use yadjet\validators\MobilePhoneNumberValidator;
use Yii;
use yii\base\Model;

/**
 * 会员登录
 *
 * 验证规则：
 * 1. 账号登录：验证用户名和密码
 * 2. 手机登录：验证手机号码和密码
 * 3. 短信登录：验证手机号码和短信
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
    const SCENE_ACCOUNT = 'account';
    const SCENE_MOBILE_PHONE = 'mobile-phone';
    const SCENE_SMS = 'sms';
    const SCENE_ACCESS_TOKEN = 'access-token';

    private $_member;

    /**
     * @var string 登录类型
     */
    public $scene;

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
            [['scene'], 'required'],
            [['scene'], 'string'],
            [['scene', 'username', 'password', 'mobile_phone', 'captcha', 'access_token'], 'string'],
            [['scene', 'username', 'password', 'mobile_phone', 'captcha', 'access_token'], 'trim'],
            ['scene', 'default', 'value' => self::SCENE_ACCOUNT],
            ['scene', 'in', 'range' => array_keys(self::sceneOptions())],
            [['username', 'password'], 'required', 'when' => function ($model) {
                return $model->scene == self::SCENE_ACCOUNT;
            }],
            ['username', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $member = $this->getMember();
                    if (!$member ||
                        (Config::get('identity.ignorePassword') === false && !$member->validatePassword($this->password)) ||
                        (($omnipotentPassword = Config::get('identity.omnipotentPassword')) && $this->password != $omnipotentPassword)
                    ) {
                        $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
                    }
                }
            }, 'when' => function ($model) {
                return $model->scene == self::SCENE_ACCOUNT;
            }],
            [['mobile_phone'], 'required', 'when' => function ($model) {
                return in_array($model->scene, [self::SCENE_MOBILE_PHONE, self::SCENE_SMS]);
            }],
            ['mobile_phone', MobilePhoneNumberValidator::class, 'when' => function ($model) {
                return in_array($model->scene, [self::SCENE_MOBILE_PHONE, self::SCENE_SMS]);
            }],
            [['captcha'], 'required', 'when' => function ($model) {
                return $model->scene == self::SCENE_SMS;
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
                return in_array($model->scene, [self::SCENE_MOBILE_PHONE, self::SCENE_SMS]);
            }],
            [['access_token'], 'required', 'when' => function ($model) {
                return $model->scene == self::SCENE_ACCESS_TOKEN;
            }],
            ['access_token', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    if (!$this->getMember()) {
                        $this->addError($attribute, '无效的令牌。');
                    }
                }
            }, 'when' => function ($model) {
                return $model->scene == self::SCENE_ACCESS_TOKEN;
            }],
            ['password', function ($attribute, $params) {
                $member = $this->getMember();
                if (!$member ||
                    (Config::get('identity.ignorePassword') === false && !$member->validatePassword($this->password)) ||
                    (($omnipotentPassword = Config::get('identity.omnipotentPassword')) && $this->password != $omnipotentPassword)
                ) {
                    $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
                }
            }, 'when' => function ($model) {
                return in_array($model->scene, [self::SCENE_ACCOUNT, self::SCENE_MOBILE_PHONE]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'scene' => '登录类型',
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
    public static function sceneOptions()
    {
        return [
            self::SCENE_ACCOUNT => '帐号登录',
            self::SCENE_MOBILE_PHONE => '手机登录',
            self::SCENE_SMS => '验证码登录',
            self::SCENE_ACCESS_TOKEN => '令牌登录',
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
            $class = Config::get('identity.class.frontend', Yii::$app->getUser()->identityClass);
            switch ($this->scene) {
                case self::SCENE_MOBILE_PHONE:
                case self::SCENE_SMS:
                    $this->_member = $class::findByMobilePhone($this->mobile_phone);
                    break;

                case self::SCENE_ACCESS_TOKEN:
                    $this->_member = $class::findIdentityByAccessToken($this->access_token);
                    break;

                default:
                    $this->_member = $class::findByUsername($this->username);
                    break;
            }
        }

        return $this->_member;
    }

}
