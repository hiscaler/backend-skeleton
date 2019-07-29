<?php

namespace app\modules\api\modules\notice\models;

use app\modules\api\modules\notice\extensions\Formatter;
use Yii;

class Notice extends \app\modules\admin\modules\notice\models\Notice
{

    public function fields()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();

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
            'view_permission_formatted' => function ($model) use ($formatter) {
                return $formatter->asNoticeViewPermission($model->view_permission);
            },
            'has_read' => function ($model) {
                return boolval($model->read);
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
