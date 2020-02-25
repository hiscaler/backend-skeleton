<?php

namespace app\models;

/**
 * 前台会员
 *
 * @package app\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class FrontendMember extends BaseMember
{

    private static function isFrontend($member)
    {
        if ($member) {
            if ($member->usable_scope != self::USABLE_SCOPE_FRONTEND && $member->usable_scope != self::USABLE_SCOPE_ALL) {
                $member = null;
            }
        }

        return $member;
    }

    public static function findByUsername($username, $type = null)
    {
        return static::isFrontend(parent::findByUsername($username, $type));
    }

    public static function findByMobilePhone($mobilePhone, $type = null)
    {
        return static::isFrontend(parent::findByMobilePhone($mobilePhone, $type));
    }

    /**
     * Finds user by wechat openid
     *
     * @param $openid
     * @return static|null
     * @throws \yii\db\Exception
     */
    public static function findByWechatOpenId($openid)
    {
        return static::isFrontend(parent::findByWechatOpenId($openid));
    }

    public static function findByWechatUnionId($unionid)
    {
        return static::isFrontend(parent::findByWechatUnionId($unionid));
    }

    public static function findIdentity($id)
    {
        return static::isFrontend(parent::findIdentity($id));
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::isFrontend(parent::findIdentityByAccessToken($token, $type));
    }

}