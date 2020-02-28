<?php

namespace app\modules\api\modules\article\models;

use app\modules\api\extensions\AppHelper;

class Article extends \app\modules\admin\modules\article\models\Article
{

    public function fields()
    {
        return [
            'id',
            'alias',
            'title',
            'keyword',
            'description',
            'content' => function ($model) {
                return AppHelper::fixContentAssetUrl($model->content);
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}
