<?php

namespace app\models;

/**
 * 后台会员
 *
 * @package app\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BackendMember extends BaseMember
{

    private static function isBackend($member)
    {
        if ($member) {
            if ($member->usable_scope != self::USABLE_SCOPE_BACKEND) {
                $member = null;
            }
        }

        return $member;
    }

    public static function findByUsername($username, $type = null)
    {
        return static::isBackend(parent::findByUsername($username, $type));
    }

    public static function findByMobilePhone($mobilePhone, $type = null)
    {
        return static::isBackend(parent::findByMobilePhone($mobilePhone, $type));
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
        return static::isBackend(parent::findByWechatOpenId($openid));
    }

    public static function findByWechatUnionId($unionid)
    {
        return static::isBackend(parent::findByWechatUnionId($unionid));
    }

    public static function findIdentity($id)
    {
        return static::isBackend(parent::findIdentity($id));
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::isBackend(parent::findIdentityByAccessToken($token, $type));
    }

}