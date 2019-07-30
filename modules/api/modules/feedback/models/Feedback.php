<?php

namespace app\modules\api\modules\feedback\models;

use app\modules\api\extensions\UtilsHelper;

class Feedback extends \app\modules\admin\modules\feedback\models\Feedback
{

    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'username',
            'tel',
            'mobile_phone',
            'email',
            'ip',
            'picture' => function ($model) {
                return UtilsHelper::fixStaticAssetUrl($model['picture']);
            },
            'message',
            'response_message',
            'response_datetime',
            'enabled' => function () {
                return boolval($this->enabled);
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}
