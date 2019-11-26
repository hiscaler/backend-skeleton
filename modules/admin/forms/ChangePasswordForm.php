<?php

namespace app\modules\admin\forms;

use yii\base\Model;

class ChangePasswordForm extends Model
{

    public $username;
    public $password;
    public $confirm_password;

    public function rules()
    {
        return [
            [['password', 'confirm_password'], 'required'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 30],
            ['confirm_password', 'compare', 'operator' => '===', 'compareAttribute' => 'password',
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
            'username' => '用户名',
            'password' => '新密码',
            'confirm_password' => '确认密码',
        ];
    }

}
