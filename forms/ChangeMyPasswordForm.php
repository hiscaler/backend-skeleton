<?php

namespace app\forms;

use yii\base\Model;

/**
 * 修改登录用户密码
 *
 * @package app\forms
 */
class ChangeMyPasswordForm extends Model
{

    public $username;
    public $old_password;
    public $password;
    public $confirm_password;

    public function rules()
    {
        return [
            ['username', 'string'],
            [['old_password', 'password', 'confirm_password'], 'required'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 30],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
            ['old_password', 'checkOldPassword'],
        ];
    }

    /**
     * 验证旧密码是否有效
     *
     * @param $attribute
     * @param $params
     * @throws \Throwable
     */
    public function checkOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $ok = \Yii::$app->getUser()->getIdentity()->validatePassword($this->old_password);
            if (!$ok) {
                $this->addError($attribute, '原密码无效。');
            }
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'username' => '帐户',
            'old_password' => '原密码',
            'password' => '新密码',
            'confirm_password' => '确认密码',
        ];
    }

}
