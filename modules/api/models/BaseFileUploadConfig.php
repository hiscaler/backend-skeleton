<?php

namespace app\modules\api\models;

/**
 * Class BaseFileUploadConfig
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseFileUploadConfig extends \app\models\FileUploadConfig
{

    public function fields()
    {
        return [
            'id',
            'type',
            'model_name',
            'attribute',
            'extensions',
            'min_size',
            'max_size',
            'thumb_width',
            'thumb_height',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}