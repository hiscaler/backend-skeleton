<?php

namespace app\modules\api\modules\link\models;

use Yii;

class Link extends \app\modules\admin\modules\link\models\Link
{

    public function fields()
    {
        return [
            'id',
            'category_id' => '',
            'type',
            'title',
            'description',
            'url',
            'url_open_target',
            'logo' => function ($model) {
                return $model->logo ? (Yii::$app->getRequest()->getHostInfo() . $model->logo) : null;
            },
            'ordering',
            'enabled',
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
