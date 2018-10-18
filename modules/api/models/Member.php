<?php

namespace app\modules\api\models;

use app\models\Meta;
use app\modules\api\components\ApplicationHelper;
use Yii;

class Member extends \app\models\Member
{

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'type',
            'categoryId' => 'category_id',
            'group',
            'username',
            'nickname',
            'realName' => 'real_name',
            'avatar' => function () {
                $avatar = $this->avatar;
                if (!empty($avatar)) {
                    $addUrl = true;
                    foreach (['http', 'https', '//'] as $prefix) {
                        if (strncasecmp($avatar, $prefix, strlen($prefix)) === 0) {
                            $addUrl = false;
                            break;
                        }
                    }

                    if ($addUrl) {
                        $avatar = Yii::$app->getRequest()->hostInfo . $avatar;
                    }
                }

                return $avatar;
            },
            'email',
            'tel',
            'mobilePhone' => 'mobile_phone',
            'address',
            'registerIp' => 'register_ip',
            'totalCredits' => 'total_credits',
            'availableCredits' => 'available_credits',
            'loginCount' => 'login_count',
            'lastLoginIp' => 'last_login_ip',
            'lastLoginTime' => 'last_login_time',
            'accessToken' => 'access_token',
            'status' => 'status',
            'remark' => 'remark',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
            'metaItems' => function () {
                $items = [];
                $rawItems = \Yii::$app->getDb()->createCommand('SELECT [[m.key]], [[m.return_value_type]], [[string_value]], [[text_value]], [[integer_value]], [[decimal_value]] FROM {{%meta_value}} t LEFT JOIN {{%meta}} m ON [[t.meta_id]] = [[m.id]] WHERE [[t.object_id]] = :objectId AND [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName)', [
                    ':objectId' => $this->id,
                    ':tableName' => strtr(static::tableName(), ['{{%' => '', '}}' => ''])
                ])->queryAll();
                foreach ($rawItems as $item) {
                    $valueKey = Meta::parseReturnKey($item['return_value_type']);
                    $items[$item['key']] = $item[$valueKey];
                }

                return $items;
            }
        ];
    }

    /**
     * @inheritdoc
     *
     * @todo 需要处理过期时间
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /**
         * Token 格式
         * 1. token值
         * 2. token值.有效的时间戳
         * 3. 类型.token值.有效的时间戳
         */
        $member = static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
        if ($member) {
            if (stripos($token, '.') === false) {
                return $member; // 1. token值
            } else {
                $tokens = explode('.', $token);
                if (isset($tokens[2])) {
                    // 3. 类型.token值.有效的时间戳
                    list (, , $expire) = $tokens;
                } else {
                    // 2. token值.有效的时间戳
                    list (, $expire) = $tokens;
                }
                $accessTokenExpire = ApplicationHelper::getConfigValue('member.accessTokenExpire', 86400);
                $accessTokenExpire = (int) $accessTokenExpire ?: 86400;

                return ((int) $expire + $accessTokenExpire) > time() ? $member : null;
            }
        }

        return null;
    }

}
