<?php

namespace app\modules\api\forms;

use app\modules\api\models\User;
use Yii;

/**
 * 用户注册
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserRegisterForm extends User
{

    public $password;
    public $confirm_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['password', 'confirm_password'], 'required'],
            [['password', 'confirm_password', 'email'], 'trim'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 12],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'password' => Yii::t('member', 'Password'),
            'confirm_password' => Yii::t('member', 'Confirm Password'),
        ]);
    }

}
