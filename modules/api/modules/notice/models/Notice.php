<?php

namespace app\modules\api\modules\notice\models;

class Notice extends \app\modules\admin\modules\notice\models\Notice
{

    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'description',
            'content',
            'enabled' => function ($model) {
                return boolval($model->enabled);
            },
            'clicks_count',
            'published_at',
            'view_permission',
            'view_permission_formatted' => function ($model) {
                $options = Notice::viewPermissionOptions();

                return isset($options[$model->view_permission]) ? $options[$model->view_permission] : null;
            },
            'ordering',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    public function extraFields()
    {
        return ['category'];
    }

}
