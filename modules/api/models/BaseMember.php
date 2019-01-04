<?php

namespace app\modules\api\models;

use app\models\Meta;
use Yii;

/**
 * Class BaseMember
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseMember extends \app\models\Member
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
            'invitationCode' => 'invitation_code',
            'parentId' => 'parent_id',
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

}
