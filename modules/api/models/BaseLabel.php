<?php

namespace app\modules\api\models;

/**
 * Class BaseLabel
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseLabel extends \app\models\Label
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
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}