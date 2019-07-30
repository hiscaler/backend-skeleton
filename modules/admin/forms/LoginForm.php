<?php

namespace app\modules\admin\forms;

use app\helpers\Config;
use app\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{

    public $username;
    public $password;
    public $remember_me = true;
    public $verify_code;
    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            // username and password are both required
            [['username', 'password'], 'required'],
            // remember_me must be a boolean value
            ['remember_me', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];

        if (Config::get('hideCaptcha') === false) {
            $rules[] = ['verify_code', 'captcha', 'captchaAction' => '/admin/default/captcha'];
        }

        return $rules;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user ||
                (Config::get('ignorePassword') === false && !$user->validatePassword($this->password)) ||
                (($omnipotentPassword = Config::get('omnipotentPassword')) && $this->password != $omnipotentPassword)
            ) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            return Yii::$app->getUser()->login($user, $this->remember_me ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '帐　号',
            'password' => '密　码',
            'verify_code' => '验证码',
            'remember_me' => '记住登录',
        ];
    }

}
