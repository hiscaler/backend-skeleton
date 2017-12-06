<?php

namespace app\modules\admin\forms;

use yii\base\Model;

class ChangePasswordForm extends Model
{

    public $password;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['password', 'confirmPassword'], 'required'],
            [['password', 'confirmPassword'], 'string', 'min' => 6, 'max' => 12],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
        ];
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'password' => '新密码',
            'confirmPassword' => '确认密码',
        ];
    }

}
