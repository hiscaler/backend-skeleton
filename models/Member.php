<?php

namespace app\models;

use Yii;

/**
 * 会员模型
 */
class Member extends User
{

    /**
     * 用户类型
     *
     * @var integer
     */
    public $type = self::TYPE_MEMBER;

    /**
     * 用户推荐码
     *
     * @var string
     */
    public $referral;

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->type = self::TYPE_MEMBER;
                $status = Lookup::getValue('user.signup.status', self::STATUS_PENDING);
                $this->status = isset(self::statusOptions()[$status]) ? $status : self::STATUS_PENDING;

                // 设置推荐人
                $referral = trim($this->referral);
                if (!empty($referral)) {
                    $userId = Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%user}} WHERE [[referral_code]] = :referralCode', [':referralCode' => $referral])->queryScalar();
                    $this->referral_user_id = $userId ?: 0;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if ($signupCredits = abs((int) Lookup::getValue('user.signup.status', 0))) {
                // 注册赠送积分
                UserCreditLog::add($this->id, UserCreditLog::OPERATION_USER_SIGNUP, $signupCredits, $this->id, null);
            }

            // 推荐人员赠送积分    
            if ($this->referral_user_id && $referralCredits = abs((int) Lookup::getValue('user.signup.referral.credits', 0))) {
                UserCreditLog::add($this->referral_user_id, UserCreditLog::OPERATION_REFERRAL_SIGNUP, $referralCredits, $this->id, $this->referral);
            }
        }
    }

}
