<?php

namespace app\forms;

use app\models\Member;
use Yii;

/**
 * 会员注册表单
 */
class MemberRegisterForm extends Member
{

    public $password;
    public $confirm_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['password', 'confirm_password', 'email'], 'required'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 30],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('site', 'Username'),
            'password' => Yii::t('site', 'Password'),
            'confirm_password' => Yii::t('site', 'Confirm password'),
            'email' => Yii::t('site', 'E-mail'),
        ];
    }

    // Events
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->type = self::TYPE_MEMBER;

            return true;
        } else {
            return false;
        }
    }

}
