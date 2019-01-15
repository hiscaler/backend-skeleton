<?php

namespace app\modules\api\models;

/**
 * Class BaseMeta
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseMeta extends \app\models\Meta
{

    public function fields()
    {
        return [
            'id',
            'table_name',
            'key',
            'label',
            'description',
            'input_type',
            'input_candidate_value',
            'return_value_type',
            'default_value',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'deleted_at',
            'deleted_by',
        ];
    }

}