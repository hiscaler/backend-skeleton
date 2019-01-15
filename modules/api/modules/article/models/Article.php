<?php

namespace app\modules\api\modules\article\models;

use app\modules\api\extensions\UtilsHelper;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property int $id
 * @property string $alias
 * @property string $title 标题
 * @property string $keyword 关键词
 * @property string $description 描述
 * @property string $content 正文
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
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
            'content' => function () {
                return UtilsHelper::fixContentAssetUrl($this->content);
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

}
