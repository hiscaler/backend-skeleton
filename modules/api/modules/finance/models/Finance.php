<?php

namespace app\modules\api\modules\finance\models;

use app\modules\api\modules\finance\extensions\Formatter;
use Yii;

class Finance extends \app\modules\admin\modules\finance\models\Finance
{

    public function fields()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();

        return [
            'id',
            'type',
            'type_formatted' => function ($model) use ($formatter) {
                return $formatter->asFinanceType($model->type);
            },
            'money' => function ($model) use ($formatter) {
                return $formatter->asYuan($model->money);
            },
            'balance' => function ($model) use ($formatter) {
                return $formatter->asYuan($model->balance);
            },
            'source',
            'source_formatted' => function ($model) use ($formatter) {
                return $formatter->asFinanceSource($model->source);
            },
            'remittance_slip' => function ($model) use ($formatter) {
                return $formatter->asAssetFullPath($model->remittance_slip);
            },
            'related_key',
            'status',
            'status_formatted' => function ($model) use ($formatter) {
                return $formatter->asFinanceStatus($model->status);
            },
            'remark',
            'member_id',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    public function extraFields()
    {
        return ['member'];
    }

}