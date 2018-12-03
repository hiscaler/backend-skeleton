<?php

namespace app\modules\api\models;

/**
 * Class Label
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class Label extends \app\models\Label
{

    public function fields()
    {
        return [
            'id',
            'alias',
            'name',
            'frequency',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'ordering',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }

}