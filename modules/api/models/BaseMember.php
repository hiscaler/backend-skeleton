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
            'type_formatted' => function ($model) {
                $options = static::typeOptions();

                return isset($options[$model->type]) ? $options[$model->type] : null;
            },
            'category_id',
            'group',
            'invitation_code',
            'parent_id',
            'username',
            'nickname',
            'real_name',
            'avatar' => function ($model) {
                $avatar = $model->avatar;
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
            'mobile_phone',
            'register_ip',
            'total_money',
            'available_money',
            'total_credits',
            'available_credits',
            'alarm_credits',
            'login_count',
            'last_login_ip',
            'last_login_time',
            'access_token',
            'status' => 'status',
            'status_formatted' => function ($model) {
                $options = static::statusOptions();

                return isset($options[$model->status]) ? $options[$model->status] : null;
            },
            'remark' => 'remark',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'meta_items' => function ($model) {
                $items = [];
                $rawItems = \Yii::$app->getDb()->createCommand('SELECT [[m.key]], [[m.return_value_type]], [[string_value]], [[text_value]], [[integer_value]], [[decimal_value]] FROM {{%meta_value}} t LEFT JOIN {{%meta}} m ON [[t.meta_id]] = [[m.id]] WHERE [[t.object_id]] = :objectId AND [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName)', [
                    ':objectId' => $model->id,
                    ':tableName' => strtr(static::tableName(), ['{{%', '}}' => ''])
                ])->queryAll();
                foreach ($rawItems as $item) {
                    $valueKey = Meta::parseReturnKey($item['return_value_type']);
                    $items[$item['key']] = $item[$valueKey];
                }

                return $items;
            }
        ];
    }

    public function extraFields()
    {
        return ['wechat', 'profile', 'creditLogs'];
    }

}
