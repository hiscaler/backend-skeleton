<?php

namespace app\modules\api\forms;

use yii\base\Model;

/**
 * 修改我的密码
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class ChangeMyPasswordForm extends Model
{

    public $oldPassword;
    public $password;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['oldPassword', 'password', 'confirmPassword'], 'required'],
            [['password', 'confirmPassword'], 'string', 'min' => 6, 'max' => 12],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password',
                'message' => '两次输入的密码不一致，请重新输入。'
            ],
            ['oldPassword', 'checkOldPassword'],
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
            $ok = \Yii::$app->getUser()->getIdentity()->validatePassword($this->oldPassword);
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
            'oldPassword' => '旧密码',
            'password' => '新密码',
            'confirmPassword' => '确认密码',
        ];
    }

}
