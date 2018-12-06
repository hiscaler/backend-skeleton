<?php

namespace app\modules\api\models;

/**
 * Class Meta
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class Meta extends \app\models\Meta
{

    public function fields()
    {
        return [
            'id',
            'tableName' => 'table_name',
            'key',
            'label',
            'description',
            'inputType' => 'input_type',
            'inputCandidateValue' => 'input_candidate_value',
            'returnValueType' => 'return_value_type',
            'defaultValue' => 'default_value',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
            'deletedAt' => 'deleted_at',
            'deletedBy' => 'deleted_by',
        ];
    }

}