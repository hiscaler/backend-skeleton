<?php

namespace app\modules\api\forms;

use app\modules\api\models\Member;
use Yii;

/**
 * 会员注册
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberRegisterForm extends Member
{

    /**
     * 会员注册方式
     * account. 账号登录：验证用户名和密码
     * mobile_phone. 手机登录：验证手机号码和密码
     * captcha. 验证码登录：验证手机号码和验证码
     */
    const REGISTER_TYPE_ACCOUNT = 'account';
    const REGISTER_TYPE_MOBILE_PHONE = 'mobile_phone';
    const REGISTER_TYPE_CAPTCHA = 'captcha';

    /**
     * @var string 注册方式
     */
    public $register_type = self::REGISTER_TYPE_ACCOUNT;

    /**
     * @var string 密码
     */
    public $password;

    /**
     * @var string 确认密码
     */
    public $confirm_password;

    /**
     * @var string 验证码
     */
    public $captcha;

    /**
     * @var string 邀请码
     */
    public $invitation_code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        $rules = [
            ['register_type', 'string'],
            ['register_type', 'trim'],
            ['register_type', 'default', 'value' => self::REGISTER_TYPE_ACCOUNT],
            ['register_type', 'in', 'range' => [self::REGISTER_TYPE_ACCOUNT, self::REGISTER_TYPE_MOBILE_PHONE, self::REGISTER_TYPE_CAPTCHA]],
            [['password', 'confirm_password'], 'required', 'when' => function ($model) {
                return in_array($model->register_type, [self::REGISTER_TYPE_ACCOUNT, self::REGISTER_TYPE_MOBILE_PHONE]);
            }],
            [['password', 'confirm_password'], 'trim',
                'when' => function ($model) {
                    return in_array($model->register_type, [self::REGISTER_TYPE_ACCOUNT, self::REGISTER_TYPE_MOBILE_PHONE]);
                }],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 30,
                'when' => function ($model) {
                    return in_array($model->register_type, [self::REGISTER_TYPE_ACCOUNT, self::REGISTER_TYPE_MOBILE_PHONE]);
                }],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。',
                'when' => function ($model) {
                    return in_array($model->register_type, [self::REGISTER_TYPE_ACCOUNT, self::REGISTER_TYPE_MOBILE_PHONE]);
                }],
            ['captcha', 'required', 'when' => function ($model) {
                return $model->register_type == self::REGISTER_TYPE_CAPTCHA;
            }],
            ['captcha', 'string', 'when' => function ($model) {
                return $model->register_type == self::REGISTER_TYPE_CAPTCHA;
            }],
            ['captcha', 'trim', 'when' => function ($model) {
                return $model->register_type == self::REGISTER_TYPE_CAPTCHA;
            }],
            ['captcha', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $cache = Yii::$app->getCache()->get(SmsForm::CACHE_PREFIX . $this->mobile_phone);
                    if ($cache === false || $cache['expired_datetime'] <= time() || $cache['value'] != $this->captcha) {
                        $this->addError($attribute, '无效的验证码。');
                    }
                }
            }, 'when' => function ($model) {
                return $model->register_type == self::REGISTER_TYPE_CAPTCHA;
            }],
            ['register_type', function ($model, $params) {
                if (in_array($this->register_type, [self::REGISTER_TYPE_MOBILE_PHONE, self::REGISTER_TYPE_CAPTCHA])) {
                    $this->username = $this->mobile_phone;
                }
            }],
        ];

        return array_merge($rules, $parentRules);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'register_type' => '注册方式',
            'password' => Yii::t('member', 'Password'),
            'confirm_password' => Yii::t('member', 'Confirm Password'),
            'captcha' => '验证码',
        ]);
    }

}
