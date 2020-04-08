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
     * 会员注册场景
     *
     * account. 账号注册：验证用户名和密码
     * mobile_phone. 手机注册：验证手机号码和密码
     * sms. 验证码注册：验证手机号码和短信验证码
     */
    const SCENE_ACCOUNT = 'account';
    const SCENE_MOBILE_PHONE = 'mobile-phone';
    const SCENE_SMS = 'sms';

    /**
     * @var string 注册方式
     */
    public $scene = self::SCENE_ACCOUNT;

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
            ['scene', 'string'],
            ['scene', 'trim'],
            ['scene', 'default', 'value' => self::SCENE_ACCOUNT],
            ['scene', 'in', 'range' => [self::SCENE_ACCOUNT, self::SCENE_MOBILE_PHONE, self::SCENE_SMS]],
            [['password', 'confirm_password'], 'required', 'when' => function ($model) {
                return in_array($model->scene, [self::SCENE_ACCOUNT, self::SCENE_MOBILE_PHONE]);
            }],
            [['password', 'confirm_password'], 'trim',
                'when' => function ($model) {
                    return in_array($model->scene, [self::SCENE_ACCOUNT, self::SCENE_MOBILE_PHONE]);
                }],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 30,
                'when' => function ($model) {
                    return in_array($model->scene, [self::SCENE_ACCOUNT, self::SCENE_MOBILE_PHONE]);
                }],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。',
                'when' => function ($model) {
                    return in_array($model->scene, [self::SCENE_ACCOUNT, self::SCENE_MOBILE_PHONE]);
                }],
            ['captcha', 'required', 'when' => function ($model) {
                return $model->scene == self::SCENE_SMS;
            }],
            ['captcha', 'string', 'when' => function ($model) {
                return $model->scene == self::SCENE_SMS;
            }],
            ['captcha', 'trim', 'when' => function ($model) {
                return $model->scene == self::SCENE_SMS;
            }],
            ['captcha', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $cache = Yii::$app->getCache()->get(SmsForm::CACHE_PREFIX . $this->mobile_phone);
                    if ($cache === false || $cache['expired_datetime'] <= time() || $cache['value'] != $this->captcha) {
                        $this->addError($attribute, '无效的验证码。');
                    }
                }
            }, 'when' => function ($model) {
                return $model->scene == self::SCENE_SMS;
            }],
            ['scene', function ($model, $params) {
                if (in_array($this->scene, [self::SCENE_MOBILE_PHONE, self::SCENE_SMS])) {
                    $this->username = $this->mobile_phone;
                }
            }],
            ['invitation_code', 'trim'],
            ['invitation_code', 'string'],
            ['invitation_code', function ($attribute, $params) {
                if ($this->invitation_code) {
                    $memberId = Yii::$app->getDb()->createCommand("SELECT [[id]] FROM {{%member}} WHERE [[unique_key]] = :uniqueKey", [
                        ':uniqueKey' => $this->invitation_code,
                    ])->queryScalar();
                    if ($memberId) {
                        $this->parent_id = $memberId;
                    } else {
                        $this->addError($attribute, '请填写正确的邀请码。');
                    }
                }
            }],
        ];

        return array_merge($rules, $parentRules);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'scene' => '注册方式',
            'password' => Yii::t('member', 'Password'),
            'confirm_password' => Yii::t('member', 'Confirm Password'),
            'captcha' => '验证码',
        ]);
    }

}
