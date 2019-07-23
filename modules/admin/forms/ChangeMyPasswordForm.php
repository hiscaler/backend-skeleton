<?php

namespace app\modules\admin\forms;

use Yii;
use yii\base\Model;

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
            ['confirm_password', 'compare', 'operator' => '===', 'compareAttribute' => 'password',
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
            $ok = Yii::$app->getUser()->getIdentity()->validatePassword($this->old_password);
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
