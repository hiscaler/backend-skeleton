<?php

namespace app\modules\api\modules\slide\models;

use app\modules\api\extensions\UtilsHelper;

/**
 * This is the model class for table "{{%slide}}".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property string $url
 * @property string $url_open_target
 * @property string $picture_path
 * @property integer $ordering
 * @property integer $enabled
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Slide extends \app\modules\admin\modules\slide\models\Slide
{

    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'url',
            'url_open_target',
            'picture_path' => function ($model) {
                return UtilsHelper::fixStaticAssetUrl($model['picture_path']);
            },
            'ordering',
            'enabled' => function ($model) {
                return boolval($model['enabled']);
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}
