<?php

namespace app\modules\api\models;

/**
 * Class Module
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \app\models\Module
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
                return $model->icon ? \Yii::$app->getRequest()->getBaseUrl() . $model->icon : null;
            },
            'url',
            'description',
            'menus' => function ($model) {
                return $model->menus ? json_decode($model->menus, true) : [];
            },
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }

}