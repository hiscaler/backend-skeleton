<?php

namespace app\forms;

use app\models\Member;
use yadjet\validators\MobilePhoneNumberValidator;
use Yii;
use yii\base\Model;

/**
 * 会员手机号码登录
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberMobilePhoneLoginForm extends Model
{

    public $mobile_phone;
    public $verification_code;
    public $rememberMe = true;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['mobile_phone', 'verification_code'], 'required'],
            ['mobile_phone', 'trim'],
            ['mobile_phone', MobilePhoneNumberValidator::class],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['verification_code', 'validateVerificationCode'],
        ];
    }

    /**
     * 验证验证码
     *
     * @param $attribute
     * @param $params
     */
    public function validateVerificationCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, '无效的会员。');
            } else {
                $cache = Yii::$app->getCache();
                $v = $cache->get("verification_code_{$this->mobile_phone}");
                if ($v == false && strcmp($v, $this->verification_code) != 0) {
                    $this->addError($attribute, '验证码有误。');
                }
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
            return Yii::$app->getUser()->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return \app\models\BaseMember
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $user = Member::findByMobilePhone($this->mobile_phone);
            $this->_user = $user;
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'mobile_phone' => '手机号：',
            'verification_code' => '验证码：',
        ];
    }

}
