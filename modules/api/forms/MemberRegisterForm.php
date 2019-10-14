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
     */
    const REGISTER_TYPE_ACCOUNT = 'account';
    const REGISTER_TYPE_MOBILE_PHONE = 'mobile_phone';

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
    public $parent_invite_code;

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
            ['register_type', 'in', 'range' => [self::REGISTER_TYPE_ACCOUNT, self::REGISTER_TYPE_MOBILE_PHONE]],
        ];
        if ($this->register_type == self::REGISTER_TYPE_ACCOUNT) {
            $rules = array_merge($rules, [
                [['password', 'confirm_password'], 'required'],
                [['password', 'confirm_password'], 'trim'],
                [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 12],
                ['confirm_password', 'compare', 'compareAttribute' => 'password',
                    'message' => '两次输入的密码不一致，请重新输入。'
                ],
            ]);
        } elseif ($this->register_type == self::REGISTER_TYPE_MOBILE_PHONE) {
            unset($parentRules['registerByUsername']);
            $this->username = $this->mobile_phone;
            $rules = array_merge($rules, [
                ['captcha', 'required'],
                ['captcha', 'string'],
                ['captcha', 'trim'],
                ['captcha', function ($attribute, $params) {
                    if (!$this->hasErrors()) {
                        $cache = Yii::$app->getCache()->get(SmsForm::CACHE_PREFIX . $this->mobile_phone);
                        if ($cache === false || $cache['expired_datetime'] <= time() || $cache['value'] != $this->captcha) {
                            $this->addError($attribute, '无效的验证码。');
                        }
                    }
                }],
            ]);
        }

        $rules = array_merge($rules, [
            ['parent_invite_code', 'string'],
            ['parent_invite_code', function ($attribute, $params) {
                if ($this->parent_invite_code) {
                    $memberId = Yii::$app->getDb()->createCommand("SELECT [[id]] FROM {{%member}} WHERE [[invitation_code]] = :inviteCode", [
                        ':inviteCode' => $this->parent_invite_code,
                    ])->queryScalar();
                    if ($memberId) {
                        $this->parent_id = $memberId;
                    } else {
                        $this->addError($attribute, '请填写正确的邀请码。');
                    }
                }
            }],
        ]);

        return array_merge($parentRules, $rules);
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

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->register_type == self::REGISTER_TYPE_MOBILE_PHONE) {
                $this->username = $this->mobile_phone;
            }

            return true;
        } else {
            return false;
        }
    }

}
