<?php

namespace app\modules\api\models;

use app\modules\api\extensions\UtilsHelper;

/**
 * Class BaseModule
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseModule extends \app\models\Module
{

    public function fields()
    {
        return [
            'id',
            'alias',
            'name',
            'author',
            'version',
            'icon' => function ($model) {
                return UtilsHelper::fixStaticAssetUrl($model->icon);
            },
            'url',
            'description',
            'menus' => function ($model) {
                return $model->menus ? json_decode($model->menus, true) : [];
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}