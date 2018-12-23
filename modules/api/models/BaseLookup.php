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
            'returnType' => 'return_type',
            'inputMethod' => 'input_method',
            'inputValue' => 'input_value',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }

}