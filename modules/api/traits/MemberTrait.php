<?php

namespace app\modules\api\traits;

use app\models\Meta;
use app\modules\api\extensions\Formatter;
use app\modules\api\extensions\UtilsHelper;
use app\modules\api\models\MemberCreditLog;
use app\modules\api\models\MemberLoginLog;
use app\modules\api\models\MemberProfile;
use Yii;

trait MemberTrait
{

    /**
     * @inheritdoc
     */
    public function fields()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();

        return [
            'id',
            'type',
            'type_formatted' => function ($model) use ($formatter) {
                return $formatter->asMemberType($model->type);
            },
            'category_id',
            'group',
            'unique_key',
            'parent_id',
            'username',
            'nickname',
            'real_name',
            'avatar' => function ($model) {
                return UtilsHelper::fixStaticAssetUrl($model->avatar);
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
            'expired_datetime',
            'status' => 'status',
            'status_formatted' => function ($model) use ($formatter) {
                return $formatter->asMemberStatus($model->status);
            },
            'usable_scope',
            'remark' => 'remark',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function getMetaItems()
    {
        $items = [];
        $rawItems = Yii::$app->getDb()->createCommand('SELECT [[m.key]], [[m.return_value_type]], [[string_value]], [[text_value]], [[integer_value]], [[decimal_value]] FROM {{%meta_value}} t LEFT JOIN {{%meta}} m ON [[t.meta_id]] = [[m.id]] WHERE [[t.object_id]] = :objectId AND [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName)', [
            ':objectId' => $this->id,
            ':tableName' => strtr(static::tableName(), ['{{%', '}}' => ''])
        ])->queryAll();
        foreach ($rawItems as $item) {
            $valueKey = Meta::parseReturnKey($item['return_value_type']);
            $items[$item['key']] = $item[$valueKey];
        }

        return $items;
    }

    /**
     * 会员资料
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(MemberProfile::class, ['member_id' => 'id']);
    }

    /**
     * 积分记录
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreditLogs()
    {
        return $this->hasMany(MemberCreditLog::class, ['member_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
    }

    /**
     * 登录日志
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoginLogs()
    {
        return $this->hasMany(MemberLoginLog::class, ['member_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
    }

    public function extraFields()
    {
        return [
            'wechat',
            'profile',
            'credit_logs' => 'creditLogs',
            'meta_items' => 'metaItems',
            'login_logs' => 'loginLogs',
            'roles',
        ];
    }

}
