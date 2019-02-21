<?php

namespace app\modules\api\forms;

use app\modules\api\models\Member;
use yii\base\Model;

/**
 * 修改我的密码
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class ChangeMyPasswordForm extends Model
{

    public $old_password;
    public $password;
    public $confirm_password;

    public function rules()
    {
        return [
            [['old_password', 'password', 'confirm_password'], 'required'],
            [['password', 'confirm_password'], 'string', 'min' => 6, 'max' => 12],
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
            $ok = false;
            if ($user = \Yii::$app->getUser()) {
                /* @var $identity Member */
                $identity = $user->getIdentity();
                $ok = $identity->validatePassword($this->old_password);
            }

            if (!$ok) {
                $this->addError($attribute, '旧密码无效。');
            }
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'old_password' => '旧密码',
            'password' => '新密码',
            'confirm_password' => '确认密码',
        ];
    }

}
