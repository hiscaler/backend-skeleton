<?php

namespace app\forms;

use yii\base\Model;

/**
 * 重置密码
 */
class ResetPasswordForm extends Model
{

    public $_user;
    public $token;
    public $password;
    public $confirm_password;

    public function rules()
    {
        return [
            [['token', 'password', 'confirm_password'], 'required'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 12],
            ['token', 'safe'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
            ['token', 'validateToken'],
        ];
    }

    public function validateToken($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError('password', Yii::t('site', 'Incorrect Token.'));
            }
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'password' => '新密码',
            'confirm_password' => '确认密码',
        ];
    }

    /**
     * Finds user by [[rest_password_token]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = \app\models\User::findByPasswordResetToken($this->token);
        }

        return $this->_user;
    }

}
