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
            'modelName' => 'model_name',
            'attribute',
            'extensions',
            'minSize' => 'min_size',
            'maxSize' => 'max_size',
            'thumbWidth' => 'thumb_width',
            'thumbHeight' => 'thumb_height',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
    }

}