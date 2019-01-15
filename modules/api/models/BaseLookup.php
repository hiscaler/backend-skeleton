<?php

namespace app\modules\api\models;

/**
 * Class BaseLookup
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseLookup extends \app\models\Lookup
{

    public function fields()
    {
        return [
            'id',
            'type',
            'group',
            'key',
            'label',
            'description',
            'value',
            'return_type',
            'input_method',
            'input_value',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}