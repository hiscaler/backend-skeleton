<?php

namespace app\modules\api\forms;

use app\modules\api\models\Member;

/**
 * 会员注册
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberRegisterForm extends Member
{

    /**
     * 注册方式
     */
    const REGISTER_BY_USERNAME = 'username';
    const REGISTER_BY_MOBILE_PHONE = 'mobile_phone';

    public $register_by = self::REGISTER_BY_USERNAME;
    public $password;
    public $confirm_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['register_by', 'string'];
        if ($this->register_by != self::REGISTER_BY_USERNAME) {
            unset($rules['registerByUsername']);
        }

        $rules = array_merge($rules, [
            [['password', 'confirm_password'], 'required'],
            [['password', 'confirm_password', 'email'], 'trim'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 12],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
        ]);

        return $rules;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'password' => \Yii::t('member', 'Password'),
            'confirm_password' => \Yii::t('member', 'Confirm Password'),
        ]);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->register_by == self::REGISTER_BY_MOBILE_PHONE) {
                $this->username = $this->mobile_phone;
            }

            return true;
        } else {
            return false;
        }
    }

}
