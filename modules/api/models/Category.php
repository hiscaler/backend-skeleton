<?php

namespace app\modules\api\models;

/**
 * Class Category
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class Category extends \app\models\Category
{

    public function fields()
    {
        return [
            'id',
            'sign',
            'alias',
            'name',
            'shortName' => 'short_name',
            'parentId' => 'parent_id',
            'level',
            'idPath' => 'id_path',
            'namePath' => 'name_path',
            'icon' => function ($model) {
                $icon = $model->icon;
                if ($icon) {
                    $icon = \Yii::$app->getRequest()->getHostInfo() . $icon;
                }

                return $icon;
            },
            'description',
            'enabled' => function ($model) {
                return $model->enabled ? true : false;
            },
            'ordering',
            'quantity',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }

}