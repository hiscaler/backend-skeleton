<?php

namespace app\forms;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * 忘记密码填写表单
 */
class ForgetPasswordForm extends Model
{

    private $_user;
    public $username;
    public $email;
    public $token;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'email'], 'required'],
            [['username', 'email'], 'trim'],
            [['email'], 'email'],
            [['email'], 'validateRequest'],
        ];
    }

    /**
     * 验证请求的帐号和邮箱是否正确
     *
     * @param string $attribute
     * @param array $params
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function validateRequest($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, Yii::t('site', 'Incorrect username or email.'));
            }
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return false|string
     * @throws \yii\db\Exception
     * @throws \yii\base\Exception
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $db = Yii::$app->getDb();
            $userId = $db->createCommand('SELECT [[id]] FROM {{%user}} WHERE [[username]] = :username AND [[email]] = :email AND [[status]] = :status', [':username' => $this->username, ':email' => $this->email, ':status' => User::STATUS_ACTIVE])->queryScalar();
            if ($userId) {
                $this->_user = $userId;
                // Update password reset token
                $this->token = Yii::$app->getSecurity()->generateRandomString() . '_' . time();
                $db->createCommand()->update('{{%user}}', ['password_reset_token' => $this->token], ['id' => $userId])->execute();
            }
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'email' => '邮箱',
        ];
    }

}
