<?php

namespace app\modules\api\models;

use app\modules\api\extensions\AppHelper;

/**
 * Class BaseCategory
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseCategory extends \app\models\Category
{

    public function fields()
    {
        return [
            'id',
            'sign',
            'alias',
            'name',
            'short_name',
            'parent_id',
            'level',
            'id_path',
            'name_path',
            'icon' => function ($model) {
                return AppHelper::fixStaticAssetUrl($model->icon);
            },
            'description',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'ordering',
            'quantity',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}